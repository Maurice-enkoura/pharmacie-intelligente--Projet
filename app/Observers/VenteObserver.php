<?php

namespace App\Observers;

use App\Models\Vente;
use App\Models\Medicament;

class VenteObserver
{
    /**
     * Handle the Vente "created" event.
     */
    public function created(Vente $vente): void
    {
        //
        foreach ($vente->ligneVentes as $ligne) {
            $medicament = Medicament::find($ligne->medicament_id);
            $medicament->stock_actuel -= $ligne->quantite;
            $medicament->save();
        }
    }

    /**
     * Handle the Vente "updated" event.
     */
    public function updated(Vente $vente): void
    {
        //
    }

    /**
     * Handle the Vente "deleted" event.
     */
    public function deleted(Vente $vente): void
    {
        //
    }

    /**
     * Handle the Vente "restored" event.
     */
    public function restored(Vente $vente): void
    {
        //
    }

    /**
     * Handle the Vente "force deleted" event.
     */
    public function forceDeleted(Vente $vente): void
    {
        //
    }
}
