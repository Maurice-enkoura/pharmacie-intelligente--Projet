<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    protected $fillable = [
        'vente_id', 'medicament_id', 'quantite', 
        'prix_unitaire', 'sous_total'
    ];
    
    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }
    
    public function medicament()
    {
        return $this->belongsTo(Medicament::class);
    }
}