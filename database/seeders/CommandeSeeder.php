<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Fournisseur;
use App\Models\Medicament;
use Carbon\Carbon;

class CommandeSeeder extends Seeder
{
    public function run(): void
    {
        $fournisseurs = Fournisseur::all();
        $medicaments = Medicament::all();
        
        if ($fournisseurs->isEmpty() || $medicaments->isEmpty()) {
            $this->command->info('Données manquantes pour les commandes');
            return;
        }
        
        // Commande 1 - En attente
        $commande1 = Commande::create([
            'numero_commande' => 'CMD-20250001',
            'fournisseur_id' => $fournisseurs[0]->id,
            'date_commande' => Carbon::now()->subDays(5),
            'statut' => 'en_attente',
            'montant_total' => (1200 * 50) + (800 * 30)
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande1->id,
            'medicament_id' => $medicaments[5]->id, // Touxal
            'quantite_commandee' => 50,
            'quantite_recue' => 0,
            'prix_unitaire' => 1200,
            'sous_total' => 1200 * 50
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande1->id,
            'medicament_id' => $medicaments[2]->id, // Ibuprofène
            'quantite_commandee' => 30,
            'quantite_recue' => 0,
            'prix_unitaire' => 800,
            'sous_total' => 800 * 30
        ]);
        
        // Commande 2 - Partiellement reçue
        $commande2 = Commande::create([
            'numero_commande' => 'CMD-20250002',
            'fournisseur_id' => $fournisseurs[1]->id,
            'date_commande' => Carbon::now()->subDays(10),
            'statut' => 'recue_partielle',
            'montant_total' => (2500 * 20) + (1500 * 40)
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande2->id,
            'medicament_id' => $medicaments[1]->id, // Paracétamol
            'quantite_commandee' => 20,
            'quantite_recue' => 15,
            'prix_unitaire' => 2500,
            'sous_total' => 2500 * 20
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande2->id,
            'medicament_id' => $medicaments[0]->id, // Amoxicilline
            'quantite_commandee' => 40,
            'quantite_recue' => 20,
            'prix_unitaire' => 1500,
            'sous_total' => 1500 * 40
        ]);
        
        // Commande 3 - Complètement reçue
        $commande3 = Commande::create([
            'numero_commande' => 'CMD-20250003',
            'fournisseur_id' => $fournisseurs[2]->id,
            'date_commande' => Carbon::now()->subDays(15),
            'statut' => 'recue_complete',
            'montant_total' => (3500 * 10) + (2000 * 25)
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande3->id,
            'medicament_id' => $medicaments[3]->id, // Nifédipine
            'quantite_commandee' => 10,
            'quantite_recue' => 10,
            'prix_unitaire' => 3500,
            'sous_total' => 3500 * 10
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande3->id,
            'medicament_id' => $medicaments[4]->id, // Vitamine C
            'quantite_commandee' => 25,
            'quantite_recue' => 25,
            'prix_unitaire' => 2000,
            'sous_total' => 2000 * 25
        ]);
        
        // Commande 4 - En attente (nouvelle)
        $commande4 = Commande::create([
            'numero_commande' => 'CMD-20250004',
            'fournisseur_id' => $fournisseurs[3]->id,
            'date_commande' => Carbon::now(),
            'statut' => 'en_attente',
            'montant_total' => (1200 * 60) + (600 * 45)
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande4->id,
            'medicament_id' => $medicaments[6]->id, // Cétirizine
            'quantite_commandee' => 60,
            'quantite_recue' => 0,
            'prix_unitaire' => 1200,
            'sous_total' => 1200 * 60
        ]);
        
        LigneCommande::create([
            'commande_id' => $commande4->id,
            'medicament_id' => $medicaments[5]->id, // Touxal
            'quantite_commandee' => 45,
            'quantite_recue' => 0,
            'prix_unitaire' => 600,
            'sous_total' => 600 * 45
        ]);
        
        $this->command->info('Commandes créées avec succès !');
    }
}