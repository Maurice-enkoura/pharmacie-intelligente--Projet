<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\LigneVente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    public function index(Request $request)
    {
        $query = Vente::query();
        
        // Filtres de période
        if ($request->has('date_debut') && $request->date_debut) {
            $query->whereDate('date_vente', '>=', $request->date_debut);
        }
        if ($request->has('date_fin') && $request->date_fin) {
            $query->whereDate('date_vente', '<=', $request->date_fin);
        }
        
        // Par défaut, dernier mois
        if (!$request->has('date_debut') && !$request->has('date_fin')) {
            $periode = $request->periode ?? 'mois';
            switch ($periode) {
                case 'semaine':
                    $query->whereDate('date_vente', '>=', now()->startOfWeek());
                    break;
                case 'mois':
                    $query->whereDate('date_vente', '>=', now()->startOfMonth());
                    break;
                case 'trimestre':
                    $query->whereDate('date_vente', '>=', now()->startOfQuarter());
                    break;
                case 'annee':
                    $query->whereDate('date_vente', '>=', now()->startOfYear());
                    break;
            }
        }
        
        // Chiffre d'affaires
        $chiffreAffaires = $query->sum('montant_total');
        
        // Nombre de ventes
        $nombreVentes = $query->count();
        
        // Panier moyen
        $panierMoyen = $nombreVentes > 0 ? $chiffreAffaires / $nombreVentes : 0;
        
        // Clients actifs (qui ont acheté pendant la période)
        $clientsActifs = $query->distinct('client_id')->count('client_id');
        
        // Top médicaments
        $topMedicaments = LigneVente::whereHas('vente', function($q) use ($request, $query) {
            if ($request->has('date_debut')) {
                $q->whereDate('date_vente', '>=', $request->date_debut);
            }
            if ($request->has('date_fin')) {
                $q->whereDate('date_vente', '<=', $request->date_fin);
            }
            if (!$request->has('date_debut') && !$request->has('date_fin')) {
                $periode = $request->periode ?? 'mois';
                switch ($periode) {
                    case 'semaine':
                        $q->whereDate('date_vente', '>=', now()->startOfWeek());
                        break;
                    case 'mois':
                        $q->whereDate('date_vente', '>=', now()->startOfMonth());
                        break;
                    case 'trimestre':
                        $q->whereDate('date_vente', '>=', now()->startOfQuarter());
                        break;
                    case 'annee':
                        $q->whereDate('date_vente', '>=', now()->startOfYear());
                        break;
                }
            }
        })
        ->join('medicaments', 'ligne_ventes.medicament_id', '=', 'medicaments.id')
        ->select('medicaments.nom', DB::raw('SUM(ligne_ventes.quantite) as total_vendus'), DB::raw('SUM(ligne_ventes.sous_total) as ca'))
        ->groupBy('medicaments.id', 'medicaments.nom')
        ->orderBy('total_vendus', 'desc')
        ->limit(10)
        ->get();
        
        // Ventes par jour
        $ventesParJour = $query->select(DB::raw('DATE(date_vente) as date'), DB::raw('SUM(montant_total) as ca'))
            ->groupBy(DB::raw('DATE(date_vente)'))
            ->orderBy('date', 'asc')
            ->get();
        
        // Paiements par mode
        $paiements = $query->select('mode_paiement', DB::raw('SUM(montant_total) as total'))
            ->groupBy('mode_paiement')
            ->get();
        
        // Ventes par caissier
        $ventesParCaissier = $query->select('users.name', DB::raw('COUNT(ventes.id) as total_ventes'))
            ->join('users', 'ventes.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->get();
        
        return response()->json([
            'chiffre_affaires' => $chiffreAffaires,
            'nombre_ventes' => $nombreVentes,
            'panier_moyen' => $panierMoyen,
            'clients_actifs' => $clientsActifs,
            'top_medicaments' => $topMedicaments,
            'ventes_par_jour' => $ventesParJour,
            'paiements' => $paiements,
            'ventes_par_caissier' => $ventesParCaissier
        ]);
    }
    
    public function exportExcel()
    {
        // À implémenter avec Maatwebsite/Excel
        return response()->json(['message' => 'Export Excel en développement']);
    }
}