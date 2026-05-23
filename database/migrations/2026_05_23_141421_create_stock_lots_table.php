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
        Schema::create('stock_lots', function (Blueprint $table) {
    $table->id();
    $table->foreignId('medicament_id')->constrained();
    $table->foreignId('fournisseur_id')->constrained();
    $table->string('lot_number');
    $table->integer('quantite_initiale');
    $table->integer('quantite_restante');
    $table->date('date_peremption');
    $table->decimal('prix_achat_unitaire', 10, 2);
    $table->date('date_reception');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_lots');
    }
};
