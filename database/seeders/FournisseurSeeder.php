<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fournisseur;

class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        $fournisseurs = [
            [
                'nom' => 'Laborex Sénégal',
                'contact' => 'M. Diallo',
                'telephone' => '338123456',
                'email' => 'contact@laborex.sn',
                'adresse' => 'Zone industrielle, Dakar'
            ],
            [
                'nom' => 'PharmaPlus International',
                'contact' => 'Mme Diouf',
                'telephone' => '338234567',
                'email' => 'commandes@pharmaplus.com',
                'adresse' => 'Route du King Fahd Palace, Almadies'
            ],
            [
                'nom' => 'AfricSanté Distribution',
                'contact' => 'M. Ndiaye',
                'telephone' => '338345678',
                'email' => 'service@africsante.sn',
                'adresse' => 'Colobane, Dakar'
            ],
            [
                'nom' => 'MediLab Sénégal',
                'contact' => 'Dr Gueye',
                'telephone' => '338456789',
                'email' => 'ventes@medilab.sn',
                'adresse' => 'Mermoz, Dakar'
            ],
        ];

        foreach ($fournisseurs as $fournisseur) {
            Fournisseur::firstOrCreate(
                ['email' => $fournisseur['email']],
                $fournisseur
            );
        }
        
        $this->command->info('Fournisseurs créés avec succès !');
    }
}