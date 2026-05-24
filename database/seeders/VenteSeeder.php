<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vente;
use App\Models\LigneVente;
use App\Models\Client;
use App\Models\Medicament;
use App\Models\User;
use Carbon\Carbon;

class VenteSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les données existantes
        $clients = Client::all();
        $medicaments = Medicament::all();
        $caissier = User::where('role', 'caissier')->first();
        
        if ($clients->isEmpty() || $medicaments->isEmpty() || !$caissier) {
            $this->command->info('Données manquantes pour les ventes');
            return;
        }
        
        // Vente 1 - Client Diop Aminata
        $vente1 = Vente::create([
            'numero_facture' => 'FACT-20250001',
            'client_id' => $clients[0]->id,
            'user_id' => $caissier->id,
            'date_vente' => Carbon::now()->subDays(2),
            'type_vente' => 'sans_ordonnance',
            'ordonnance_ref' => null,
            'montant_total' => 3100,
            'montant_paye' => 3200,
            'monnaie_rendue' => 100,
            'mode_paiement' => 'especes'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente1->id,
            'medicament_id' => $medicaments[1]->id, // Paracétamol (prix 2500)
            'quantite' => 1,
            'prix_unitaire' => 2500,
            'sous_total' => 2500
        ]);
        
        LigneVente::create([
            'vente_id' => $vente1->id,
            'medicament_id' => $medicaments[2]->id, // Ibuprofène (prix 600)
            'quantite' => 1,
            'prix_unitaire' => 600,
            'sous_total' => 600
        ]);
        
        // Vente 2 - Client Fall Mamadou
        $vente2 = Vente::create([
            'numero_facture' => 'FACT-20250002',
            'client_id' => $clients[1]->id,
            'user_id' => $caissier->id,
            'date_vente' => Carbon::now()->subDays(1),
            'type_vente' => 'avec_ordonnance',
            'ordonnance_ref' => 'ORD-2025-001',
            'montant_total' => 3500,
            'montant_paye' => 3500,
            'monnaie_rendue' => 0,
            'mode_paiement' => 'orange_money'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente2->id,
            'medicament_id' => $medicaments[3]->id, // Nifédipine (prix 3500)
            'quantite' => 1,
            'prix_unitaire' => 3500,
            'sous_total' => 3500
        ]);
        
        // Vente 3 - Client Ndiaye Fatou
        $vente3 = Vente::create([
            'numero_facture' => 'FACT-20250003',
            'client_id' => $clients[2]->id,
            'user_id' => $caissier->id,
            'date_vente' => Carbon::now(),
            'type_vente' => 'sans_ordonnance',
            'ordonnance_ref' => null,
            'montant_total' => 5200,
            'montant_paye' => 6000,
            'monnaie_rendue' => 800,
            'mode_paiement' => 'wave'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente3->id,
            'medicament_id' => $medicaments[4]->id, // Vitamine C (prix 2000)
            'quantite' => 2,
            'prix_unitaire' => 2000,
            'sous_total' => 4000
        ]);
        
        LigneVente::create([
            'vente_id' => $vente3->id,
            'medicament_id' => $medicaments[6]->id, // Cétirizine (prix 1200)
            'quantite' => 1,
            'prix_unitaire' => 1200,
            'sous_total' => 1200
        ]);
        
        // Vente 4 - Client Sow Oumar
        $vente4 = Vente::create([
            'numero_facture' => 'FACT-20250004',
            'client_id' => $clients[3]->id,
            'user_id' => $caissier->id,
            'date_vente' => Carbon::now()->subDays(3),
            'type_vente' => 'sans_ordonnance',
            'ordonnance_ref' => null,
            'montant_total' => 1500,
            'montant_paye' => 2000,
            'monnaie_rendue' => 500,
            'mode_paiement' => 'especes'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente4->id,
            'medicament_id' => $medicaments[0]->id, // Amoxicilline (prix 1500)
            'quantite' => 1,
            'prix_unitaire' => 1500,
            'sous_total' => 1500
        ]);
        
        $this->command->info('Ventes créées avec succès !');
    }
}