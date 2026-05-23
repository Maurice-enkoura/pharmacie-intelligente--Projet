<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\Medicament;
use App\Models\Vente;

use App\Policies\MedicamentPolicy;
use App\Policies\VentePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Medicament::class => MedicamentPolicy::class,
        Vente::class => VentePolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}