<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 
        'adresse', 'medicaments_chroniques'
    ];
    
    protected $casts = [
        'medicaments_chroniques' => 'array'
    ];
    
    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }
}