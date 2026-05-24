<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Antibiotique', 'description' => 'Médicaments contre les infections bactériennes'],
            ['nom' => 'Antalgique', 'description' => 'Médicaments contre la douleur'],
            ['nom' => 'Anti-inflammatoire', 'description' => 'Médicaments contre l\'inflammation'],
            ['nom' => 'Antihypertenseur', 'description' => 'Médicaments contre l\'hypertension'],
            ['nom' => 'Vitamines', 'description' => 'Compléments vitaminiques'],
            ['nom' => 'Sirop', 'description' => 'Médicaments sous forme liquide'],
            ['nom' => 'Dermatologique', 'description' => 'Médicaments pour la peau'],
        ];

        foreach ($categories as $cat) {
            Categorie::firstOrCreate(
                ['nom' => $cat['nom']],
                ['description' => $cat['description']]
            );
        }
        
        $this->command->info('Catégories créées avec succès !');
    }
}