<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Models\LigneVente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Récupérer la période
            $periode = $request->periode ?? 'mois';
            $dateDebut = null;
            $dateFin = null;
            
            if ($request->filled('date_debut') && $request->filled('date_fin')) {
                $dateDebut = $request->date_debut;
                $dateFin = $request->date_fin;
            } else {
                switch ($periode) {
                    case 'semaine':
                        $dateDebut = now()->startOfWeek()->format('Y-m-d');
                        $dateFin = now()->endOfWeek()->format('Y-m-d');
                        break;
                    case 'mois':
                        $dateDebut = now()->startOfMonth()->format('Y-m-d');
                        $dateFin = now()->endOfMonth()->format('Y-m-d');
                        break;
                    case 'trimestre':
                        $dateDebut = now()->startOfQuarter()->format('Y-m-d');
                        $dateFin = now()->endOfQuarter()->format('Y-m-d');
                        break;
                    case 'annee':
                        $dateDebut = now()->startOfYear()->format('Y-m-d');
                        $dateFin = now()->endOfYear()->format('Y-m-d');
                        break;
                    default:
                        $dateDebut = now()->startOfMonth()->format('Y-m-d');
                        $dateFin = now()->endOfMonth()->format('Y-m-d');
                        break;
                }
            }
            
            // Requête de base filtrée par date
            $ventesQuery = Vente::query();
            if ($dateDebut) {
                $ventesQuery->whereDate('date_vente', '>=', $dateDebut);
            }
            if ($dateFin) {
                $ventesQuery->whereDate('date_vente', '<=', $dateFin);
            }
            
            // 1. Chiffre d'affaires
            $chiffreAffaires = (float) $ventesQuery->sum('montant_total');
            
            // 2. Nombre de ventes
            $nombreVentes = (int) $ventesQuery->count();
            
            // 3. Panier moyen
            $panierMoyen = $nombreVentes > 0 ? (float) ($chiffreAffaires / $nombreVentes) : 0;
            
            // 4. Clients actifs
            $clientsActifs = (int) $ventesQuery->distinct('client_id')->count('client_id');
            
            // 5. Top médicaments
            $topMedicaments = LigneVente::whereHas('vente', function($q) use ($dateDebut, $dateFin) {
                    if ($dateDebut) $q->whereDate('date_vente', '>=', $dateDebut);
                    if ($dateFin) $q->whereDate('date_vente', '<=', $dateFin);
                })
                ->join('medicaments', 'ligne_ventes.medicament_id', '=', 'medicaments.id')
                ->select('medicaments.nom', DB::raw('SUM(ligne_ventes.quantite) as total_vendus'))
                ->groupBy('medicaments.id', 'medicaments.nom')
                ->orderBy('total_vendus', 'desc')
                ->limit(5)
                ->get();
            
            // 6. Ventes par jour (graphique)
            $ventesParJour = Vente::select(DB::raw('DATE(date_vente) as date'), DB::raw('SUM(montant_total) as ca'))
                ->when($dateDebut, function($q) use ($dateDebut) {
                    $q->whereDate('date_vente', '>=', $dateDebut);
                })
                ->when($dateFin, function($q) use ($dateFin) {
                    $q->whereDate('date_vente', '<=', $dateFin);
                })
                ->groupBy(DB::raw('DATE(date_vente)'))
                ->orderBy('date', 'asc')
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->date,
                        'ca' => (float) $item->ca
                    ];
                });
            
            // 7. Paiements par mode
            $paiements = Vente::select('mode_paiement', DB::raw('SUM(montant_total) as total'))
                ->when($dateDebut, function($q) use ($dateDebut) {
                    $q->whereDate('date_vente', '>=', $dateDebut);
                })
                ->when($dateFin, function($q) use ($dateFin) {
                    $q->whereDate('date_vente', '<=', $dateFin);
                })
                ->groupBy('mode_paiement')
                ->get()
                ->map(function($item) {
                    return [
                        'mode_paiement' => $item->mode_paiement,
                        'total' => (float) $item->total
                    ];
                });
            
            // 8. Ventes par caissier
            $ventesParCaissier = Vente::select('users.name', DB::raw('COUNT(ventes.id) as total_ventes'))
                ->join('users', 'ventes.user_id', '=', 'users.id')
                ->when($dateDebut, function($q) use ($dateDebut) {
                    $q->whereDate('ventes.date_vente', '>=', $dateDebut);
                })
                ->when($dateFin, function($q) use ($dateFin) {
                    $q->whereDate('ventes.date_vente', '<=', $dateFin);
                })
                ->groupBy('users.id', 'users.name')
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->name,
                        'total_ventes' => (int) $item->total_ventes
                    ];
                });
            
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
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors du chargement des rapports',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    
    public function exportExcel()
    {
        return response()->json(['message' => 'Export Excel en développement']);
    }
}