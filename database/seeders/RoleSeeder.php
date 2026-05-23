<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;    

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Admin
    User::create([
        'name' => 'Admin Principal',
        'email' => 'admin@pharmacie.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);
    
    // Pharmacien
    User::create([
        'name' => 'Dr Diallo',
        'email' => 'pharmacien@pharmacie.com',
        'password' => bcrypt('password'),
        'role' => 'pharmacien',
    ]);
    
    // Caissier
    User::create([
        'name' => 'Aliou Ndiaye',
        'email' => 'caissier@pharmacie.com',
        'password' => bcrypt('password'),
        'role' => 'caissier',
    ]);
    }
}
