<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\Fournisseur;
use App\Models\Medicament;
use App\Models\LigneCommande;
use App\Models\StockLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CommandeController extends Controller
{
    // Liste des commandes
    public function index(Request $request)
    {
        $query = Commande::with(['fournisseur', 'ligneCommandes.medicament']);
        
        // Filtre par fournisseur
        if ($request->has('fournisseur_id')) {
            $query->where('fournisseur_id', $request->fournisseur_id);
        }
        
        // Filtre par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtre par période
        if ($request->has('date_debut')) {
            $query->whereDate('date_commande', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin')) {
            $query->whereDate('date_commande', '<=', $request->date_fin);
        }
        
        $commandes = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json($commandes);
    }
    
    // Détail d'une commande
    public function show($id)
    {
        $commande = Commande::with(['fournisseur', 'ligneCommandes.medicament'])
            ->findOrFail($id);
        
        return response()->json($commande);
    }
    
    // Créer un bon de commande
    public function store(Request $request)
    {
        // Vérifier permission (admin ou pharmacien)
        if (Gate::denies('modify', Medicament::class)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $request->validate([
            'fournisseur_id' => 'required|exists:fournisseurs,id',
            'lignes' => 'required|array|min:1',
            'lignes.*.medicament_id' => 'required|exists:medicaments,id',
            'lignes.*.quantite' => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Calculer le montant total
            $montantTotal = 0;
            foreach ($request->lignes as $ligne) {
                $montantTotal += $ligne['quantite'] * $ligne['prix_unitaire'];
            }
            
            // Créer la commande
            $commande = Commande::create([
                'numero_commande' => 'CMD-' . strtoupper(uniqid()),
                'fournisseur_id' => $request->fournisseur_id,
                'date_commande' => now(),
                'statut' => 'en_attente',
                'montant_total' => $montantTotal
            ]);
            
            // Créer les lignes de commande
            foreach ($request->lignes as $ligne) {
                LigneCommande::create([
                    'commande_id' => $commande->id,
                    'medicament_id' => $ligne['medicament_id'],
                    'quantite_commandee' => $ligne['quantite'],
                    'quantite_recue' => 0,
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'sous_total' => $ligne['quantite'] * $ligne['prix_unitaire']
                ]);
            }
            
            DB::commit();
            
            $commande->load(['fournisseur', 'ligneCommandes.medicament']);
            
            return response()->json($commande, 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Réceptionner une commande (mise à jour stock)
    public function reception(Request $request, $id)
    {
        // Vérifier permission (admin ou pharmacien)
        if (Gate::denies('modify', Medicament::class)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $commande = Commande::with('ligneCommandes.medicament')->findOrFail($id);
        
        if ($commande->statut === 'recue_complete') {
            return response()->json(['message' => 'Commande déjà complètement réceptionnée'], 400);
        }
        
        $request->validate([
            'lignes' => 'required|array',
            'lignes.*.ligne_commande_id' => 'required|exists:ligne_commandes,id',
            'lignes.*.quantite_recue' => 'required|integer|min:0'
        ]);
        
        DB::beginTransaction();
        
        try {
            $allReceived = true;
            
            foreach ($request->lignes as $ligneData) {
                $ligneCommande = LigneCommande::find($ligneData['ligne_commande_id']);
                
                // Vérifier que la ligne appartient bien à la commande
                if ($ligneCommande->commande_id != $commande->id) {
                    continue;
                }
                
                $nouvelleQuantiteRecue = $ligneCommande->quantite_recue + $ligneData['quantite_recue'];
                
                if ($nouvelleQuantiteRecue > $ligneCommande->quantite_commandee) {
                    throw new \Exception('Quantité reçue dépasse la quantité commandée');
                }
                
                // Mettre à jour la quantité reçue
                $ligneCommande->update([
                    'quantite_recue' => $nouvelleQuantiteRecue
                ]);
                
                // Si on a reçu des médicaments, créer un lot de stock
                if ($ligneData['quantite_recue'] > 0) {
                    $medicament = Medicament::find($ligneCommande->medicament_id);
                    
                    // Créer le lot de stock
                    StockLot::create([
                        'medicament_id' => $ligneCommande->medicament_id,
                        'fournisseur_id' => $commande->fournisseur_id,
                        'lot_number' => 'LOT-' . strtoupper(uniqid()),
                        'quantite_initiale' => $ligneData['quantite_recue'],
                        'quantite_restante' => $ligneData['quantite_recue'],
                        'date_peremption' => now()->addMonths(12), // À définir avec le vrai date
                        'prix_achat_unitaire' => $ligneCommande->prix_unitaire,
                        'date_reception' => now()
                    ]);
                    
                    // Mettre à jour le stock_actuel du médicament
                    $medicament->stock_actuel += $ligneData['quantite_recue'];
                    $medicament->save();
                }
                
                // Vérifier si la ligne est complètement reçue
                if ($ligneCommande->quantite_recue < $ligneCommande->quantite_commandee) {
                    $allReceived = false;
                }
            }
            
            // Mettre à jour le statut de la commande
            $nouveauStatut = $allReceived ? 'recue_complete' : 'recue_partielle';
            $commande->update(['statut' => $nouveauStatut]);
            
            DB::commit();
            
            $commande->load(['fournisseur', 'ligneCommandes.medicament']);
            
            return response()->json([
                'commande' => $commande,
                'message' => $nouveauStatut === 'recue_complete' 
                    ? 'Commande complètement réceptionnée' 
                    : 'Réception partielle enregistrée'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de la réception',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Annuler une commande
    public function destroy($id)
    {
        // Seul admin peut annuler
        if (Gate::denies('delete', Medicament::class)) {
            return response()->json(['message' => 'Seul l\'admin peut annuler une commande'], 403);
        }
        
        $commande = Commande::findOrFail($id);
        
        if ($commande->statut !== 'en_attente') {
            return response()->json(['message' => 'Seules les commandes en attente peuvent être annulées'], 400);
        }
        
        $commande->delete();
        
        return response()->json(['message' => 'Commande annulée']);
    }
    
    // Liste des commandes par fournisseur
    public function byFournisseur($fournisseurId)
    {
        $fournisseur = Fournisseur::findOrFail($fournisseurId);
        
        $commandes = Commande::where('fournisseur_id', $fournisseurId)
            ->with('ligneCommandes.medicament')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'fournisseur' => $fournisseur,
            'commandes' => $commandes
        ]);
    }
}