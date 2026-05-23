<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MedicamentController;
use App\Http\Controllers\Api\V1\VenteController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\FournisseurController;
use App\Http\Controllers\Api\V1\CommandeController;
use App\Http\Controllers\Api\V1\StockController;
use App\Http\Controllers\Api\V1\DashboardController;

// Routes publiques
Route::prefix('v1')->group(function () {
    
    // Authentification (public)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Routes protégées par authentication
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Déconnexion
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
        // ========== MÉDICAMENTS ==========
        // Tout le monde peut voir (authentifié)
        Route::get('/medicaments', [MedicamentController::class, 'index']);
        Route::get('/medicaments/{id}', [MedicamentController::class, 'show']);
        
        // Admin et pharmacien peuvent modifier
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::post('/medicaments', [MedicamentController::class, 'store']);
            Route::put('/medicaments/{id}', [MedicamentController::class, 'update']);
        });
        
        // Seul admin peut supprimer (soft delete)
        Route::middleware(['role:admin'])->group(function () {
            Route::delete('/medicaments/{id}', [MedicamentController::class, 'destroy']);
            Route::post('/medicaments/{id}/restore', [MedicamentController::class, 'restore']);
        });
        
        // ========== VENTES ==========
        // Tout le monde peut voir les ventes
        Route::get('/ventes', [VenteController::class, 'index']);
        Route::get('/ventes/{id}', [VenteController::class, 'show']);
        
        // Caissier, pharmacien et admin peuvent créer une vente
        Route::middleware(['role:admin,pharmacien,caissier'])->group(function () {
            Route::post('/ventes', [VenteController::class, 'store']);
        });
        
        // Admin et pharmacien peuvent annuler
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::delete('/ventes/{id}/cancel', [VenteController::class, 'cancel']);
        });
        
        // ========== CLIENTS ==========
        // Tout le monde peut voir
        Route::get('/clients', [ClientController::class, 'index']);
        Route::get('/clients/{id}', [ClientController::class, 'show']);
        
        // Admin et pharmacien peuvent modifier
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::post('/clients', [ClientController::class, 'store']);
            Route::put('/clients/{id}', [ClientController::class, 'update']);
        });
        
        // ========== FOURNISSEURS ==========
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::get('/fournisseurs', [FournisseurController::class, 'index']);
            Route::get('/fournisseurs/{id}', [FournisseurController::class, 'show']);
            Route::post('/fournisseurs', [FournisseurController::class, 'store']);
            Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update']);
            Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'destroy']);
        });
        
        // ========== COMMANDES ==========
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::get('/commandes', [CommandeController::class, 'index']);
            Route::get('/commandes/{id}', [CommandeController::class, 'show']);
            Route::post('/commandes', [CommandeController::class, 'store']);
            Route::put('/commandes/{id}/reception', [CommandeController::class, 'reception']);
        });
        
        // ========== STOCK & ALERTES ==========
        // Tout le monde peut voir les alertes
        Route::get('/stock/alertes', [StockController::class, 'alertes']);
        
        // Admin et pharmacien gèrent les entrées de stock
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::post('/stock/entrees', [StockController::class, 'entrees']);
            Route::get('/stock/historique', [StockController::class, 'historique']);
        });
        
        // ========== DASHBOARD & STATS ==========
        // Admin et pharmacien voient le dashboard
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index']);
        });
        
        // Admin peut voir les rapports Excel
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/rapports/excel', [DashboardController::class, 'exportExcel']);
        });
    });
});