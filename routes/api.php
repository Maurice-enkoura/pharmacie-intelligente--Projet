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
use App\Http\Controllers\Api\V1\CategorieController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\RapportController;

// Routes publiques
Route::prefix('v1')->group(function () {
    
    // Authentification (public)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Routes protégées
    Route::middleware(['auth:sanctum'])->group(function () {
        
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
        // ========== CATÉGORIES (tous) ==========
        Route::get('/categories', [CategorieController::class, 'index']);
        
        // ========== EMAIL PDF ==========
        Route::get('/ventes/{id}/send-email', [VenteController::class, 'sendEmail']);
        Route::get('/ventes/{id}/pdf', [VenteController::class, 'generatePDF']);
        
        // ========== MÉDICAMENTS ==========
        Route::get('/medicaments', [MedicamentController::class, 'index']);
        Route::get('/medicaments/{id}', [MedicamentController::class, 'show']);
        
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::post('/medicaments', [MedicamentController::class, 'store']);
            Route::put('/medicaments/{id}', [MedicamentController::class, 'update']);
        });
        
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/medicaments/export', [MedicamentController::class, 'export']);
            Route::post('/medicaments/import', [MedicamentController::class, 'import']);
            Route::get('/medicaments/template', [MedicamentController::class, 'downloadTemplate']);
            Route::delete('/medicaments/{id}', [MedicamentController::class, 'destroy']);
            Route::post('/medicaments/{id}/restore', [MedicamentController::class, 'restore']);
        });
        
        // ========== VENTES ==========
        Route::get('/ventes', [VenteController::class, 'index']);
        Route::get('/ventes/{id}', [VenteController::class, 'show']);
        Route::post('/ventes', [VenteController::class, 'store']);
        
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::delete('/ventes/{id}/cancel', [VenteController::class, 'cancel']);
        });
        
        // ========== CLIENTS (🔥 CORRIGÉ) ==========
        // Tous les authentifiés peuvent voir et créer
        Route::get('/clients', [ClientController::class, 'index']);
        Route::get('/clients/{id}', [ClientController::class, 'show']);
        Route::post('/clients', [ClientController::class, 'store']);  // 🔥 TOUT LE MONDE
        
        // Seul admin et pharmacien peuvent modifier et supprimer
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::put('/clients/{id}', [ClientController::class, 'update']);
            Route::delete('/clients/{id}', [ClientController::class, 'destroy']);
        });
        
        // ========== FOURNISSEURS ==========
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/fournisseurs', [FournisseurController::class, 'index']);
            Route::get('/fournisseurs/{id}', [FournisseurController::class, 'show']);
            Route::post('/fournisseurs', [FournisseurController::class, 'store']);
            Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update']);
            Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'destroy']);
        });
        
        // ========== COMMANDES ==========
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/commandes', [CommandeController::class, 'index']);
            Route::get('/commandes/{id}', [CommandeController::class, 'show']);
            Route::post('/commandes', [CommandeController::class, 'store']);
            Route::put('/commandes/{id}/reception', [CommandeController::class, 'reception']);
        });
        
        // ========== STOCK & ALERTES ==========
        Route::get('/stock/alertes', [StockController::class, 'alertes']);
        
        Route::middleware(['role:admin,pharmacien'])->group(function () {
            Route::post('/stock/entrees', [StockController::class, 'entrees']);
            Route::get('/stock/historique', [StockController::class, 'historique']);
        });
        
        // ========== DASHBOARD ==========
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index']);
        });
        
        // ========== RAPPORTS ==========
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/rapports', [RapportController::class, 'index']);
            Route::get('/rapports/excel', [RapportController::class, 'exportExcel']);
        });
        
        // ========== UTILISATEURS ==========
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/utilisateurs', [UserController::class, 'index']);
            Route::post('/utilisateurs', [UserController::class, 'store']);
            Route::put('/utilisateurs/{id}', [UserController::class, 'update']);
            Route::delete('/utilisateurs/{id}', [UserController::class, 'destroy']);
        });
    });
});