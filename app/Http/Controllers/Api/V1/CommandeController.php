<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Medicament;
use App\Models\LigneCommande;
use App\Models\StockLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CommandeController extends Controller
{
    // Liste des commandes
    public function index(Request $request)
    {
        try {
            $query = Commande::with(['fournisseur', 'ligneCommandes.medicament']);
            
            if ($request->has('fournisseur_id') && $request->fournisseur_id) {
                $query->where('fournisseur_id', $request->fournisseur_id);
            }
            
            if ($request->has('statut') && $request->statut) {
                $query->where('statut', $request->statut);
            }
            
            if ($request->has('date_debut') && $request->date_debut) {
                $query->whereDate('date_commande', '>=', $request->date_debut);
            }
            
            if ($request->has('date_fin') && $request->date_fin) {
                $query->whereDate('date_commande', '<=', $request->date_fin);
            }
            
            $commandes = $query->orderBy('created_at', 'desc')->paginate(15);
            
            return response()->json($commandes);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des commandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Détail d'une commande
    public function show($id)
    {
        try {
            $commande = Commande::with(['fournisseur', 'ligneCommandes.medicament'])->findOrFail($id);
            return response()->json($commande);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }
    }
    
    // Créer un bon de commande
    public function store(Request $request)
    {
        try {
            $request->validate([
                'fournisseur_id' => 'required|exists:fournisseurs,id',
                'lignes' => 'required|array|min:1',
                'lignes.*.medicament_id' => 'required|exists:medicaments,id',
                'lignes.*.quantite' => 'required|integer|min:1',
                'lignes.*.prix_unitaire' => 'required|numeric|min:0'
            ]);
            
            DB::beginTransaction();
            
            // Calculer le montant total
            $montantTotal = 0;
            foreach ($request->lignes as $ligne) {
                $montantTotal += $ligne['quantite'] * $ligne['prix_unitaire'];
            }
            
            // Créer la commande
            $commande = Commande::create([
                'numero_commande' => 'CMD-' . strtoupper(uniqid()),
                'fournisseur_id' => $request->fournisseur_id,
                'date_commande' => now(),
                'statut' => 'en_attente',
                'montant_total' => $montantTotal
            ]);
            
            // Créer les lignes de commande
            foreach ($request->lignes as $ligne) {
                LigneCommande::create([
                    'commande_id' => $commande->id,
                    'medicament_id' => $ligne['medicament_id'],
                    'quantite_commandee' => $ligne['quantite'],
                    'quantite_recue' => 0,
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total' => $ligne['quantite'] * $ligne['prix_unitaire']
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => $commande
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Réceptionner une commande
    public function reception(Request $request, $id)
    {
        try {
            $commande = Commande::with('ligneCommandes.medicament')->findOrFail($id);
            
            if ($commande->statut === 'recue_complete') {
                return response()->json(['message' => 'Commande déjà complètement réceptionnée'], 400);
            }
            
            $request->validate([
                'lignes' => 'required|array',
                'lignes.*.ligne_commande_id' => 'required|exists:ligne_commandes,id',
                'lignes.*.quantite_recue' => 'required|integer|min:0'
            ]);
            
            DB::beginTransaction();
            
            $allReceived = true;
            
            foreach ($request->lignes as $ligneData) {
                $ligneCommande = LigneCommande::find($ligneData['ligne_commande_id']);
                
                if ($ligneCommande->commande_id != $commande->id) {
                    continue;
                }
                
                $nouvelleQuantiteRecue = $ligneCommande->quantite_recue + $ligneData['quantite_recue'];
                
                if ($nouvelleQuantiteRecue > $ligneCommande->quantite_commandee) {
                    throw new \Exception('Quantité reçue dépasse la quantité commandée');
                }
                
                $ligneCommande->update([
                    'quantite_recue' => $nouvelleQuantiteRecue
                ]);
                
                if ($ligneData['quantite_recue'] > 0) {
                    $medicament = Medicament::find($ligneCommande->medicament_id);
                    
                    StockLot::create([
                        'medicament_id' => $ligneCommande->medicament_id,
                        'fournisseur_id' => $commande->fournisseur_id,
                        'lot_number' => 'LOT-' . strtoupper(uniqid()),
                        'quantite_initiale' => $ligneData['quantite_recue'],
                        'quantite_restante' => $ligneData['quantite_recue'],
                        'date_peremption' => now()->addMonths(12),
                        'prix_achat_unitaire' => $ligneCommande->prix_unitaire,
                        'date_reception' => now()
                    ]);
                    
                    $medicament->stock_actuel += $ligneData['quantite_recue'];
                    $medicament->save();
                }
                
                if ($ligneCommande->quantite_recue < $ligneCommande->quantite_commandee) {
                    $allReceived = false;
                }
            }
            
            $nouveauStatut = $allReceived ? 'recue_complete' : 'recue_partielle';
            $commande->update(['statut' => $nouveauStatut]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $nouveauStatut === 'recue_complete' ? 'Commande complètement réceptionnée' : 'Réception partielle enregistrée',
                'data' => $commande
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réception: ' . $e->getMessage()
            ], 500);
        }
    }
}