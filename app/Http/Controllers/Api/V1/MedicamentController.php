<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicament;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MedicamentController extends Controller
{
    // Liste des médicaments avec filtres
    public function index(Request $request)
    {
        $query = Medicament::with('categorie');
        
        // Filtres
        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }
        
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('dci', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->has('ordonnance_requise')) {
            $query->where('ordonnance_requise', $request->ordonnance_requise);
        }
        
        $medicaments = $query->paginate(15);
        
        return response()->json($medicaments);
    }
    
    // Détail d'un médicament
    public function show($id)
    {
        $medicament = Medicament::with(['categorie', 'stockLots' => function($q) {
            $q->orderBy('date_peremption', 'asc');
        }])->findOrFail($id);
        
        return response()->json($medicament);
    }
    
    // Créer un médicament (admin ou pharmacien)
    public function store(Request $request)
    {
        if (Gate::denies('modify', Medicament::class)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'dci' => 'required|string',
            'forme' => 'required|string',
            'dosage' => 'required|string',
            'categorie_id' => 'required|exists:categories,id',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'seuil_alerte' => 'integer|min:0',
            'ordonnance_requise' => 'boolean'
        ]);
        
        $medicament = Medicament::create($request->all());
        
        return response()->json($medicament, 201);
    }
    
    // Modifier un médicament (admin ou pharmacien)
    public function update(Request $request, $id)
    {
        $medicament = Medicament::findOrFail($id);
        
        if (Gate::denies('modify', $medicament)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $request->validate([
            'nom' => 'string|max:255',
            'prix_vente' => 'numeric|min:0',
            'seuil_alerte' => 'integer|min:0'
        ]);
        
        $medicament->update($request->all());
        
        return response()->json($medicament);
    }
    
    // Supprimer (soft delete) - admin uniquement
    public function destroy($id)
    {
        $medicament = Medicament::findOrFail($id);
        
        if (Gate::denies('delete', $medicament)) {
            return response()->json(['message' => 'Seul l\'admin peut supprimer'], 403);
        }
        
        $medicament->delete();
        
        return response()->json(['message' => 'Médicament archivé']);
    }
    
    // Restaurer un médicament (admin)
    public function restore($id)
    {
        $medicament = Medicament::withTrashed()->findOrFail($id);
        
        if (Gate::denies('delete', $medicament)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $medicament->restore();
        
        return response()->json(['message' => 'Médicament restauré']);
    }
}