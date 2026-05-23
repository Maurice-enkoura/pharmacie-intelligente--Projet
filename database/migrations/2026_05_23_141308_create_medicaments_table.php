<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('medicaments', function (Blueprint $table) {
    $table->id();
    $table->string('nom');
    $table->string('dci');  // Dénomination Commune Internationale
    $table->string('forme'); // Comprimé, Sirop, Injectable, etc.
    $table->string('dosage'); // 500mg, 10ml, etc.
    $table->foreignId('categorie_id')->constrained();
    $table->integer('stock_actuel')->default(0);
    $table->integer('seuil_alerte')->default(10);
    $table->decimal('prix_achat', 10, 2);
    $table->decimal('prix_vente', 10, 2);
    $table->boolean('ordonnance_requise')->default(false);
    $table->softDeletes();  // ⚠️ Soft delete exigé
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};
