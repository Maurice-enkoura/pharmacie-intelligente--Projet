<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // database/seeders/CategorieSeeder.php
public function run(): void
{
    $categories = ['Antibiotique', 'Antalgique', 'Anti-inflammatoire', 'Antihypertenseur', 'Vitamines', 'Sirop', 'Dermatologique'];
    
    foreach ($categories as $cat) {
        Categorie::create(['nom' => $cat]);
    }
}
}
