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
        Schema::create('ventes', function (Blueprint $table) {
    $table->id();
    $table->string('numero_facture')->unique();
    $table->foreignId('client_id')->constrained();
    $table->foreignId('user_id')->constrained(); // caissier/pharmacien
    $table->date('date_vente');
    $table->enum('type_vente', ['avec_ordonnance', 'sans_ordonnance']);
    $table->text('ordonnance_ref')->nullable(); // référence si ordonnance
    $table->decimal('montant_total', 10, 2);
    $table->decimal('montant_paye', 10, 2);
    $table->decimal('monnaie_rendue', 10, 2)->default(0);
    $table->enum('mode_paiement', ['especes', 'orange_money', 'wave', 'carte']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
