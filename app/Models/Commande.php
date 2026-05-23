<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $fillable = [
        'numero_commande', 'fournisseur_id', 'date_commande',
        'statut', 'montant_total'
    ];
    
    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }
    
    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class);
    }
}