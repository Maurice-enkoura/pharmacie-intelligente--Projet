<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FournisseurController extends Controller
{
    public function index(Request $request)
    {
        $query = Fournisseur::query();
        
        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('contact', 'like', '%' . $request->search . '%');
        }
        
        $fournisseurs = $query->paginate(15);
        
        return response()->json($fournisseurs);
    }
    
    public function show($id)
    {
        $fournisseur = Fournisseur::with(['commandes', 'stockLots.medicament'])
            ->findOrFail($id);
        
        return response()->json($fournisseur);
    }
    
    public function store(Request $request)
    {
        if (Gate::denies('modify', \App\Models\Medicament::class)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'contact' => 'required|string',
            'telephone' => 'required|string|unique:fournisseurs',
            'email' => 'required|email|unique:fournisseurs',
            'adresse' => 'required|string'
        ]);
        
        $fournisseur = Fournisseur::create($request->all());
        
        return response()->json($fournisseur, 201);
    }
    
    public function update(Request $request, $id)
    {
        if (Gate::denies('modify', \App\Models\Medicament::class)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $fournisseur = Fournisseur::findOrFail($id);
        
        $request->validate([
            'nom' => 'string|max:255',
            'telephone' => 'string|unique:fournisseurs,telephone,' . $id,
            'email' => 'email|unique:fournisseurs,email,' . $id
        ]);
        
        $fournisseur->update($request->all());
        
        return response()->json($fournisseur);
    }
    
    public function destroy($id)
    {
        if (Gate::denies('delete', \App\Models\Medicament::class)) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();
        
        return response()->json(['message' => 'Fournisseur supprimé']);
    }
}