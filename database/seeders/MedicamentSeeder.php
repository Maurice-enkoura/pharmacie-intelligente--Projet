<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicament;
use App\Models\Categorie;
use App\Models\StockLot;

class MedicamentSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les catégories existantes
        $categories = Categorie::all()->keyBy('nom');
        
        $medicaments = [
            [
                'nom' => 'Amoxicilline',
                'dci' => 'Amoxicilline',
                'forme' => 'Gélule',
                'dosage' => '500mg',
                'categorie_id' => $categories['Antibiotique']->id,
                'stock_actuel' => 150,
                'seuil_alerte' => 20,
                'prix_achat' => 1500,
                'prix_vente' => 2500,
                'ordonnance_requise' => true
            ],
            [
                'nom' => 'Paracétamol',
                'dci' => 'Paracétamol',
                'forme' => 'Comprimé',
                'dosage' => '1000mg',
                'categorie_id' => $categories['Antalgique']->id,
                'stock_actuel' => 500,
                'seuil_alerte' => 50,
                'prix_achat' => 300,
                'prix_vente' => 600,
                'ordonnance_requise' => false
            ],
            [
                'nom' => 'Ibuprofène',
                'dci' => 'Ibuprofène',
                'forme' => 'Comprimé',
                'dosage' => '400mg',
                'categorie_id' => $categories['Anti-inflammatoire']->id,
                'stock_actuel' => 200,
                'seuil_alerte' => 30,
                'prix_achat' => 800,
                'prix_vente' => 1500,
                'ordonnance_requise' => false
            ],
            [
                'nom' => 'Nifédipine',
                'dci' => 'Nifédipine',
                'forme' => 'Gélule',
                'dosage' => '30mg',
                'categorie_id' => $categories['Antihypertenseur']->id,
                'stock_actuel' => 80,
                'seuil_alerte' => 15,
                'prix_achat' => 2000,
                'prix_vente' => 3500,
                'ordonnance_requise' => true
            ],
            [
                'nom' => 'Vitamine C',
                'dci' => 'Acide ascorbique',
                'forme' => 'Comprimé effervescent',
                'dosage' => '1000mg',
                'categorie_id' => $categories['Vitamines']->id,
                'stock_actuel' => 300,
                'seuil_alerte' => 40,
                'prix_achat' => 1000,
                'prix_vente' => 2000,
                'ordonnance_requise' => false
            ],
            [
                'nom' => 'Touxal',
                'dci' => 'Dextrométhorphane',
                'forme' => 'Sirop',
                'dosage' => '15mg/5ml',
                'categorie_id' => $categories['Sirop']->id,
                'stock_actuel' => 120,
                'seuil_alerte' => 25,
                'prix_achat' => 1200,
                'prix_vente' => 2200,
                'ordonnance_requise' => false
            ],
            [
                'nom' => 'Cétirizine',
                'dci' => 'Cétirizine',
                'forme' => 'Comprimé',
                'dosage' => '10mg',
                'categorie_id' => $categories['Dermatologique']->id,
                'stock_actuel' => 180,
                'seuil_alerte' => 20,
                'prix_achat' => 600,
                'prix_vente' => 1200,
                'ordonnance_requise' => false
            ],
        ];

        foreach ($medicaments as $medicament) {
            $med = Medicament::create($medicament);
            
            // Créer un stock lot pour chaque médicament
            StockLot::create([
                'medicament_id' => $med->id,
                'fournisseur_id' => rand(1, 4),
                'lot_number' => 'LOT-' . strtoupper(uniqid()),
                'quantite_initiale' => $medicament['stock_actuel'],
                'quantite_restante' => $medicament['stock_actuel'],
                'date_peremption' => now()->addMonths(rand(6, 24)),
                'prix_achat_unitaire' => $medicament['prix_achat'],
                'date_reception' => now()->subDays(rand(1, 30))
            ]);
        }
    }
}