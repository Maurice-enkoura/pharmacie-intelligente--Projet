<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@pharmacie.com'],
            [
                'name' => 'Admin Principal',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        
        // Pharmacien
        User::firstOrCreate(
            ['email' => 'pharmacien@pharmacie.com'],
            [
                'name' => 'Dr Diallo',
                'password' => Hash::make('password'),
                'role' => 'pharmacien',
            ]
        );
        
        // Caissier
        User::firstOrCreate(
            ['email' => 'caissier@pharmacie.com'],
            [
                'name' => 'Aliou Ndiaye',
                'password' => Hash::make('password'),
                'role' => 'caissier',
            ]
        );
        
        $this->command->info('Utilisateurs créés avec succès !');
    }
}