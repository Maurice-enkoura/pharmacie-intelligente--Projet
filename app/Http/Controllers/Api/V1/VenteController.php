<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\Client;
use App\Models\Medicament;
use App\Models\LigneVente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;   

class VenteController extends Controller
{
    // Liste des ventes
    public function index(Request $request)
    {
        $query = Vente::with(['client', 'user', 'ligneVentes.medicament']);
        
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        
        if ($request->has('date_debut')) {
            $query->whereDate('date_vente', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin')) {
            $query->whereDate('date_vente', '<=', $request->date_fin);
        }
        
        $ventes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json($ventes);
    }
    
    // Détail d'une vente
    public function show($id)
    {
        $vente = Vente::with(['client', 'user', 'ligneVentes.medicament'])->findOrFail($id);
        
        return response()->json($vente);
    }
    
    // Enregistrer une vente
    public function store(Request $request)
    {
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
        
        // Calculer le montant total
        $montantTotal = 0;
        foreach ($request->lignes as $ligne) {
            $medicament = Medicament::find($ligne['medicament_id']);
            
            // Vérifier le stock
            if ($medicament->stock_actuel < $ligne['quantite']) {
                return response()->json([
                    'message' => "Stock insuffisant pour {$medicament->nom}"
                ], 400);
            }
            
            // Vérifier ordonnance si requis
            if ($medicament->ordonnance_requise && $request->type_vente === 'sans_ordonnance') {
                return response()->json([
                    'message' => "Ordonnance requise pour {$medicament->nom}"
                ], 400);
            }
            
            $montantTotal += $medicament->prix_vente * $ligne['quantite'];
        }
        
        $monnaieRendue = $request->montant_paye - $montantTotal;
        
        if ($monnaieRendue < 0) {
            return response()->json(['message' => 'Montant payé insuffisant'], 400);
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
        }
        
        $vente->load(['client', 'ligneVentes.medicament']);
        
        return response()->json($vente, 201);
    }
    
    // Annuler une vente (admin ou pharmacien)
    public function cancel($id)
    {
        $vente = Vente::findOrFail($id);
        
        if (Gate::denies('cancel', $vente)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        // Restaurer les stocks (via l'observer)
        $vente->delete();
        
        return response()->json(['message' => 'Vente annulée']);
    }
}