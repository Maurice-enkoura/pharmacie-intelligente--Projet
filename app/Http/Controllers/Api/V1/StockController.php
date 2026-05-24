<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicament;
use App\Models\StockLot;
use Illuminate\Http\Request;

class StockController extends Controller
{
    // Alertes stocks
    public function alertes()
    {
        try {
            // Stock bas
            $stockBas = Medicament::whereColumn('stock_actuel', '<', 'seuil_alerte')->get();
            
            // Péremption proche (< 30 jours)
            $peremptionProche = StockLot::whereDate('date_peremption', '<=', now()->addDays(30))
                ->where('quantite_restante', '>', 0)
                ->with('medicament')
                ->get();
            
            return response()->json([
                'stock_bas' => $stockBas,
                'peremption_proche' => $peremptionProche
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors du chargement des alertes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Entrée de stock
    public function entrees(Request $request)
    {
        try {
            $request->validate([
                'medicament_id' => 'required|exists:medicaments,id',
                'fournisseur_id' => 'required|exists:fournisseurs,id',
                'lot_number' => 'required|string',
                'quantite' => 'required|integer|min:1',
                'date_peremption' => 'required|date|after:today',
                'prix_achat' => 'required|numeric|min:0'
            ]);
            
            $stockLot = StockLot::create([
                'medicament_id' => $request->medicament_id,
                'fournisseur_id' => $request->fournisseur_id,
                'lot_number' => $request->lot_number,
                'quantite_initiale' => $request->quantite,
                'quantite_restante' => $request->quantite,
                'date_peremption' => $request->date_peremption,
                'prix_achat_unitaire' => $request->prix_achat,
                'date_reception' => now()
            ]);
            
            // Mettre à jour le stock du médicament
            $medicament = Medicament::find($request->medicament_id);
            $medicament->stock_actuel += $request->quantite;
            $medicament->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Entrée de stock enregistrée',
                'data' => $stockLot
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Historique des mouvements
    public function historique(Request $request)
    {
        try {
            $medicamentId = $request->medicament_id;
            
            $entrees = StockLot::where('medicament_id', $medicamentId)
                ->with('fournisseur')
                ->get();
            
            $sorties = \App\Models\LigneVente::whereHas('vente', function($q) {
                    $q->whereNotNull('id');
                })
                ->where('medicament_id', $medicamentId)
                ->with('vente.client')
                ->get();
            
            return response()->json([
                'entrees' => $entrees,
                'sorties' => $sorties
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}