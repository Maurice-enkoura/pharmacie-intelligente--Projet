<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vente;
use App\Models\LigneVente;
use App\Models\Client;
use App\Models\Medicament;
use App\Models\User;

class VenteSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $medicaments = Medicament::all();
        $caissier = User::where('role', 'caissier')->first();
        
        // Vente 1 - Client Diop Aminata
        $vente1 = Vente::create([
            'numero_facture' => 'FACT-20250001',
            'client_id' => $clients[0]->id,
            'user_id' => $caissier->id,
            'date_vente' => now()->subDays(2),
            'type_vente' => 'sans_ordonnance',
            'ordonnance_ref' => null,
            'montant_total' => 2500 + 600,
            'montant_paye' => 3200,
            'monnaie_rendue' => 100,
            'mode_paiement' => 'especes'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente1->id,
            'medicament_id' => $medicaments[1]->id, // Paracétamol
            'quantite' => 1,
            'prix_unitaire' => 2500,
            'sous_total' => 2500
        ]);
        
        LigneVente::create([
            'vente_id' => $vente1->id,
            'medicament_id' => $medicaments[2]->id, // Ibuprofène
            'quantite' => 1,
            'prix_unitaire' => 600,
            'sous_total' => 600
        ]);
        
        // Vente 2 - Client Fall Mamadou
        $vente2 = Vente::create([
            'numero_facture' => 'FACT-20250002',
            'client_id' => $clients[1]->id,
            'user_id' => $caissier->id,
            'date_vente' => now()->subDays(1),
            'type_vente' => 'avec_ordonnance',
            'ordonnance_ref' => 'ORD-2025-001',
            'montant_total' => 3500,
            'montant_paye' => 3500,
            'monnaie_rendue' => 0,
            'mode_paiement' => 'orange_money'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente2->id,
            'medicament_id' => $medicaments[3]->id, // Nifédipine
            'quantite' => 1,
            'prix_unitaire' => 3500,
            'sous_total' => 3500
        ]);
        
        // Vente 3 - Client Ndiaye Fatou
        $vente3 = Vente::create([
            'numero_facture' => 'FACT-20250003',
            'client_id' => $clients[2]->id,
            'user_id' => $caissier->id,
            'date_vente' => now(),
            'type_vente' => 'sans_ordonnance',
            'ordonnance_ref' => null,
            'montant_total' => (2000 * 2) + 1200,
            'montant_paye' => 6000,
            'monnaie_rendue' => 800,
            'mode_paiement' => 'wave'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente3->id,
            'medicament_id' => $medicaments[4]->id, // Vitamine C
            'quantite' => 2,
            'prix_unitaire' => 2000,
            'sous_total' => 4000
        ]);
        
        LigneVente::create([
            'vente_id' => $vente3->id,
            'medicament_id' => $medicaments[6]->id, // Cétirizine
            'quantite' => 1,
            'prix_unitaire' => 1200,
            'sous_total' => 1200
        ]);
        
        // Vente 4 - Client Sow Oumar
        $vente4 = Vente::create([
            'numero_facture' => 'FACT-20250004',
            'client_id' => $clients[3]->id,
            'user_id' => $caissier->id,
            'date_vente' => now()->subDays(3),
            'type_vente' => 'sans_ordonnance',
            'ordonnance_ref' => null,
            'montant_total' => 1500,
            'montant_paye' => 2000,
            'monnaie_rendue' => 500,
            'mode_paiement' => 'especes'
        ]);
        
        LigneVente::create([
            'vente_id' => $vente4->id,
            'medicament_id' => $medicaments[0]->id, // Amoxicilline
            'quantite' => 1,
            'prix_unitaire' => 1500,
            'sous_total' => 1500
        ]);
        
        // Mettre à jour les stocks après ventes (via l'observer normalement)
        // Mais on le fait manuellement pour les seeders
        $this->updateStocksAfterSales();
    }
    
    private function updateStocksAfterSales(): void
    {
        // Paracétamol: stock_actuel 500 - 1 = 499
        $paracetamol = Medicament::find(2);
        $paracetamol->stock_actuel = 499;
        $paracetamol->save();
        
        // Ibuprofène: stock_actuel 200 - 1 = 199
        $ibuprofene = Medicament::find(3);
        $ibuprofene->stock_actuel = 199;
        $ibuprofene->save();
        
        // Nifédipine: stock_actuel 80 - 1 = 79
        $nifedipine = Medicament::find(4);
        $nifedipine->stock_actuel = 79;
        $nifedipine->save();
        
        // Vitamine C: stock_actuel 300 - 2 = 298
        $vitamineC = Medicament::find(5);
        $vitamineC->stock_actuel = 298;
        $vitamineC->save();
        
        // Cétirizine: stock_actuel 180 - 1 = 179
        $cetirizine = Medicament::find(7);
        $cetirizine->stock_actuel = 179;
        $cetirizine->save();
        
        // Amoxicilline: stock_actuel 150 - 1 = 149
        $amoxicilline = Medicament::find(1);
        $amoxicilline->stock_actuel = 149;
        $amoxicilline->save();
    }
}