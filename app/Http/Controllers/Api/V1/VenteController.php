<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\Client;
use App\Models\Medicament;
use App\Models\LigneVente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VenteController extends Controller
{
    // Liste des ventes
    public function index(Request $request)
    {
        try {
            $query = Vente::with(['client', 'user', 'ligneVentes.medicament']);
            
            // Si c'est un caissier, il ne voit que ses propres ventes
            if (auth()->user() && auth()->user()->role === 'caissier') {
                $query->where('user_id', auth()->id());
            }
            
            if ($request->has('client_id') && $request->client_id) {
                $query->where('client_id', $request->client_id);
            }
            
            if ($request->has('date_debut') && $request->date_debut) {
                $query->whereDate('date_vente', '>=', $request->date_debut);
            }
            
            if ($request->has('date_fin') && $request->date_fin) {
                $query->whereDate('date_vente', '<=', $request->date_fin);
            }
            
            $ventes = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json($ventes);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des ventes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Détail d'une vente
    public function show($id)
    {
        try {
            $vente = Vente::with(['client', 'user', 'ligneVentes.medicament'])->findOrFail($id);
            return response()->json($vente);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Vente non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    // Enregistrer une vente
    public function store(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'lignes' => 'required|array|min:1',
                'lignes.*.medicament_id' => 'required|exists:medicaments,id',
                'lignes.*.quantite' => 'required|integer|min:1',
                'mode_paiement' => 'required|in:especes,orange_money,wave,carte',
                'montant_paye' => 'required|numeric|min:0',
                'type_vente' => 'required|in:avec_ordonnance,sans_ordonnance',
                'ordonnance_ref' => 'required_if:type_vente,avec_ordonnance'
            ]);
            
            DB::beginTransaction();
            
            // Calculer le montant total
            $montantTotal = 0;
            foreach ($request->lignes as $ligne) {
                $medicament = Medicament::find($ligne['medicament_id']);
                
                if (!$medicament) {
                    throw new \Exception('Médicament non trouvé');
                }
                
                if ($medicament->stock_actuel < $ligne['quantite']) {
                    throw new \Exception("Stock insuffisant pour {$medicament->nom}");
                }
                
                if ($medicament->ordonnance_requise && $request->type_vente === 'sans_ordonnance') {
                    throw new \Exception("Ordonnance requise pour {$medicament->nom}");
                }
                
                $montantTotal += $medicament->prix_vente * $ligne['quantite'];
            }
            
            $monnaieRendue = $request->montant_paye - $montantTotal;
            
            if ($monnaieRendue < 0) {
                throw new \Exception('Montant payé insuffisant');
            }
            
            // Créer la vente
            $vente = Vente::create([
                'numero_facture' => 'FACT-' . strtoupper(uniqid()),
                'client_id' => $request->client_id,
                'user_id' => auth()->id(),
                'date_vente' => now(),
                'type_vente' => $request->type_vente,
                'ordonnance_ref' => $request->ordonnance_ref,
                'montant_total' => $montantTotal,
                'montant_paye' => $request->montant_paye,
                'monnaie_rendue' => $monnaieRendue,
                'mode_paiement' => $request->mode_paiement
            ]);
            
            // Créer les lignes de vente
            foreach ($request->lignes as $ligne) {
                $medicament = Medicament::find($ligne['medicament_id']);
                
                LigneVente::create([
                    'vente_id' => $vente->id,
                    'medicament_id' => $ligne['medicament_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $medicament->prix_vente,
                    'sous_total' => $medicament->prix_vente * $ligne['quantite']
                ]);
                
                // Mettre à jour le stock
                $medicament->stock_actuel -= $ligne['quantite'];
                $medicament->save();
            }
            
            DB::commit();
            
            $vente->load(['client', 'ligneVentes.medicament']);
            
            return response()->json($vente, 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    // Annuler une vente
    public function cancel($id)
    {
        try {
            $vente = Vente::findOrFail($id);
            
            if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'pharmacien') {
                return response()->json(['message' => 'Non autorisé'], 403);
            }
            
            // Restaurer les stocks
            foreach ($vente->ligneVentes as $ligne) {
                $medicament = Medicament::find($ligne->medicament_id);
                if ($medicament) {
                    $medicament->stock_actuel += $ligne->quantite;
                    $medicament->save();
                }
            }
            
            $vente->delete();
            
            return response()->json(['message' => 'Vente annulée']);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'annulation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}