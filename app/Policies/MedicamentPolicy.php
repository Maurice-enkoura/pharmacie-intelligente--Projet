<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Medicament;

class MedicamentPolicy
{
    // Admin et pharmacien peuvent modifier
    public function modify(User $user)
    {
        return $user->isAdmin() || $user->isPharmacien();
    }

    // Tout le monde peut voir
    public function view(User $user)
    {
        return true;
    }

    // Seul admin peut supprimer
    public function delete(User $user)
    {
        return $user->isAdmin();
    }
}