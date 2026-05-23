<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicament;
use App\Models\StockLot;
use Illuminate\Http\Request;

class StockController extends Controller
{
    // Alertes stocks (stock < seuil OU péremption < 30 jours)
    public function alertes()
    {
        // Stock bas
        $stockBas = Medicament::whereColumn('stock_actuel', '<', 'seuil_alerte')->get();
        
        // Péremption proche
        $lotsPerimes = StockLot::whereDate('date_peremption', '<=', now()->addDays(30))
                               ->where('quantite_restante', '>', 0)
                               ->with('medicament')
                               ->get();
        
        return response()->json([
            'stock_bas' => $stockBas,
            'peremption_proche' => $lotsPerimes
        ]);
    }
    
    // Entrée de stock
    public function entrees(Request $request)
    {
        $request->validate([
            'medicament_id' => 'required|exists:medicaments,id',
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'lot_number' => 'required|string',
            'quantite' => 'required|integer|min:1',
            'date_peremption' => 'required|date',
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
        
        return response()->json($stockLot, 201);
    }
    
    // Historique des mouvements de stock
    public function historique(Request $request)
    {
        $medicamentId = $request->medicament_id;
        
        $entrees = StockLot::where('medicament_id', $medicamentId)
                           ->with('fournisseur')
                           ->get();
        
        $sorties = \App\Models\LigneVente::whereHas('vente', function($q) {
                $q->whereNotNull('id');
            })
            ->where('medicament_id', $medicamentId)
            ->with('vente')
            ->get();
        
        return response()->json([
            'entrees' => $entrees,
            'sorties' => $sorties
        ]);
    }
}