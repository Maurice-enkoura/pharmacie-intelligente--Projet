<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%')
                  ->orWhere('telephone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $clients = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return response()->json($clients);
    }
    
    public function show($id)
    {
        $client = Client::with('ventes.ligneVentes.medicament')->findOrFail($id);
        return response()->json($client);
    }
    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'telephone' => 'required|string|unique:clients,telephone',
                'email' => 'nullable|email|unique:clients,email',
                'adresse' => 'nullable|string',
                'medicaments_chroniques' => 'nullable|string'
            ]);
            
            $client = Client::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'telephone' => $request->telephone,
                'email' => $request->email,
                'adresse' => $request->adresse,
                'medicaments_chroniques' => $request->medicaments_chroniques
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Client créé avec succès',
                'data' => $client
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            
            $request->validate([
                'nom' => 'sometimes|string|max:255',
                'prenom' => 'sometimes|string|max:255',
                'telephone' => 'sometimes|string|unique:clients,telephone,' . $id,
                'email' => 'nullable|email|unique:clients,email,' . $id,
                'adresse' => 'nullable|string',
                'medicaments_chroniques' => 'nullable|string'
            ]);
            
            $client->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Client modifié avec succès',
                'data' => $client
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}