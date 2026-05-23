<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\Medicament;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->periode ?? 'mois';
        
        $dateDebut = match($periode) {
            'semaine' => now()->startOfWeek(),
            'mois' => now()->startOfMonth(),
            'annee' => now()->startOfYear(),
            default => now()->startOfMonth()
        };
        
        // Chiffre d'affaires
        $ca = Vente::whereDate('date_vente', '>=', $dateDebut)
                   ->sum('montant_total');
        
        // Nombre de ventes
        $nbVentes = Vente::whereDate('date_vente', '>=', $dateDebut)
                         ->count();
        
        // Top médicaments vendus
        $topMedicaments = DB::table('ligne_ventes')
            ->join('medicaments', 'ligne_ventes.medicament_id', '=', 'medicaments.id')
            ->join('ventes', 'ligne_ventes.vente_id', '=', 'ventes.id')
            ->whereDate('ventes.date_vente', '>=', $dateDebut)
            ->select('medicaments.nom', DB::raw('SUM(ligne_ventes.quantite) as total_vendus'))
            ->groupBy('medicaments.id', 'medicaments.nom')
            ->orderBy('total_vendus', 'desc')
            ->limit(5)
            ->get();
        
        // Stock alerte
        $stockAlerte = Medicament::whereColumn('stock_actuel', '<', 'seuil_alerte')->count();
        
        // Nombre de clients
        $nbClients = Client::count();
        
        // Ventes par mode de paiement
        $paiements = Vente::whereDate('date_vente', '>=', $dateDebut)
            ->select('mode_paiement', DB::raw('SUM(montant_total) as total'))
            ->groupBy('mode_paiement')
            ->get();
        
        return response()->json([
            'chiffre_affaires' => $ca,
            'nombre_ventes' => $nbVentes,
            'top_medicaments' => $topMedicaments,
            'stock_alerte' => $stockAlerte,
            'nombre_clients' => $nbClients,
            'paiements' => $paiements,
            'periode' => $periode
        ]);
    }
}