<?php

namespace App\Observers;

use App\Models\StockLot;
use App\Models\Medicament;
use Illuminate\Support\Facades\Log;

class StockLotObserver
{
    /**
     * Handle the StockLot "created" event.
     */
    public function created(StockLot $stockLot): void
    {
         $medicament = Medicament::find($stockLot->medicament_id);
        $medicament->stock_actuel += $stockLot->quantite_restante;
        $medicament->save();
        
        // Vérifier alerte
        $this->checkAlert($medicament);
    }

    private function checkAlert($medicament)
    {
        if ($medicament->stock_actuel < $medicament->seuil_alerte) {
            Log::warning("Alerte stock: {$medicament->nom} (stock: {$medicament->stock_actuel})");
        }
    }

    /**
     * Handle the StockLot "updated" event.
     */
    public function updated(StockLot $stockLot): void
    {
        //
    }

    /**
     * Handle the StockLot "deleted" event.
     */
    public function deleted(StockLot $stockLot): void
    {
        //
    }

    /**
     * Handle the StockLot "restored" event.
     */
    public function restored(StockLot $stockLot): void
    {
        //
    }

    /**
     * Handle the StockLot "force deleted" event.
     */
    public function forceDeleted(StockLot $stockLot): void
    {
        //
    }
}
