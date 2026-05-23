<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = [
        'nom', 'contact', 'telephone', 'email', 'adresse'
    ];
    
    public function stockLots()
    {
        return $this->hasMany(StockLot::class);
    }
    
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}