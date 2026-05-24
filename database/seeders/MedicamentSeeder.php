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
        $categories = Categorie::all();
        
        // Créer un tableau associatif nom => id
        $categoriesMap = [];
        foreach ($categories as $cat) {
            $categoriesMap[$cat->nom] = $cat->id;
        }
        
        // Vérifier que toutes les catégories existent
        $requiredCategories = ['Antibiotique', 'Antalgique', 'Anti-inflammatoire', 'Antihypertenseur', 'Vitamines', 'Sirop', 'Dermatologique'];
        
        foreach ($requiredCategories as $catName) {
            if (!isset($categoriesMap[$catName])) {
                $this->command->error("Catégorie '$catName' non trouvée !");
                return;
            }
        }
        
        $medicaments = [
            [
                'nom' => 'Amoxicilline',
                'dci' => 'Amoxicilline',
                'forme' => 'Gélule',
                'dosage' => '500mg',
                'categorie_nom' => 'Antibiotique',
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
                'categorie_nom' => 'Antalgique',
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
                'categorie_nom' => 'Anti-inflammatoire',
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
                'categorie_nom' => 'Antihypertenseur',
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
                'categorie_nom' => 'Vitamines',
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
                'categorie_nom' => 'Sirop',
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
                'categorie_nom' => 'Dermatologique',
                'stock_actuel' => 180,
                'seuil_alerte' => 20,
                'prix_achat' => 600,
                'prix_vente' => 1200,
                'ordonnance_requise' => false
            ],
        ];

        $fournisseurs = \App\Models\Fournisseur::all();
        $fournisseurIds = $fournisseurs->pluck('id')->toArray();
        
        if (empty($fournisseurIds)) {
            $this->command->error('Aucun fournisseur trouvé !');
            return;
        }

        foreach ($medicaments as $medicament) {
            $med = Medicament::create([
                'nom' => $medicament['nom'],
                'dci' => $medicament['dci'],
                'forme' => $medicament['forme'],
                'dosage' => $medicament['dosage'],
                'categorie_id' => $categoriesMap[$medicament['categorie_nom']],
                'stock_actuel' => $medicament['stock_actuel'],
                'seuil_alerte' => $medicament['seuil_alerte'],
                'prix_achat' => $medicament['prix_achat'],
                'prix_vente' => $medicament['prix_vente'],
                'ordonnance_requise' => $medicament['ordonnance_requise']
            ]);
            
            // Créer un stock lot pour chaque médicament
            StockLot::create([
                'medicament_id' => $med->id,
                'fournisseur_id' => $fournisseurIds[array_rand($fournisseurIds)],
                'lot_number' => 'LOT-' . strtoupper(uniqid()),
                'quantite_initiale' => $medicament['stock_actuel'],
                'quantite_restante' => $medicament['stock_actuel'],
                'date_peremption' => now()->addMonths(rand(6, 24)),
                'prix_achat_unitaire' => $medicament['prix_achat'],
                'date_reception' => now()->subDays(rand(1, 30))
            ]);
        }
        
        $this->command->info('Médicaments créés avec succès !');
    }
}