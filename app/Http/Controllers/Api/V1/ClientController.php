<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();
        
        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('prenom', 'like', '%' . $request->search . '%')
                  ->orWhere('telephone', 'like', '%' . $request->search . '%');
        }
        
        $clients = $query->paginate(15);
        
        return response()->json($clients);
    }
    
    public function show($id)
    {
        $client = Client::with('ventes.ligneVentes.medicament')->findOrFail($id);
        
        return response()->json($client);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'telephone' => 'required|string|unique:clients',
            'email' => 'nullable|email',
            'adresse' => 'nullable|string'
        ]);
        
        $client = Client::create($request->all());
        
        return response()->json($client, 201);
    }
    
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        
        $request->validate([
            'nom' => 'string',
            'prenom' => 'string',
            'telephone' => 'string|unique:clients,telephone,' . $id,
            'email' => 'nullable|email'
        ]);
        
        $client->update($request->all());
        
        return response()->json($client);
    }
}