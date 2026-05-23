<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'nom' => 'Diop',
                'prenom' => 'Aminata',
                'telephone' => '771234567',
                'email' => 'aminata.diop@example.com',
                'adresse' => 'Dakar, Sicap Liberté',
                'medicaments_chroniques' => json_encode(['Amlodipine 5mg', 'Metformine 500mg'])
            ],
            [
                'nom' => 'Fall',
                'prenom' => 'Mamadou',
                'telephone' => '772345678',
                'email' => 'mamadou.fall@example.com',
                'adresse' => 'Pikine, Guinaw Rails',
                'medicaments_chroniques' => null
            ],
            [
                'nom' => 'Ndiaye',
                'prenom' => 'Fatou',
                'telephone' => '773456789',
                'email' => 'fatou.ndiaye@example.com',
                'adresse' => 'Guediawaye',
                'medicaments_chroniques' => json_encode(['Salbutamol inhalateur'])
            ],
            [
                'nom' => 'Sow',
                'prenom' => 'Oumar',
                'telephone' => '774567890',
                'email' => 'oumar.sow@example.com',
                'adresse' => 'Rufisque',
                'medicaments_chroniques' => null
            ],
            [
                'nom' => 'Ba',
                'prenom' => 'Mariama',
                'telephone' => '775678901',
                'email' => 'mariama.ba@example.com',
                'adresse' => 'Thiès',
                'medicaments_chroniques' => json_encode(['Lévothyroxine 100mcg'])
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}