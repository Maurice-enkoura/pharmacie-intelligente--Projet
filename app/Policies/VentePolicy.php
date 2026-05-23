<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vente;

class VentePolicy
{
    // Tout le monde peut voir les ventes
    public function view(User $user, Vente $vente)
    {
        return true;
    }
    
    // Admin et pharmacien peuvent tout faire
    public function create(User $user)
    {
        return $user->isAdmin() || $user->isPharmacien() || $user->isCaissier();
    }
    
    // Modifier une vente (admin uniquement)
    public function update(User $user, Vente $vente)
    {
        return $user->isAdmin();
    }
    
    // Annuler une vente
    public function cancel(User $user, Vente $vente)
    {
        return $user->isAdmin() || $user->isPharmacien();
    }
    
    // Voir les rapports
    public function viewReports(User $user)
    {
        return $user->isAdmin() || $user->isPharmacien();
    }
}