// database/migrations/xxxx_xx_xx_xxxxxx_update_date_vente_in_ventes_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventes', function (Blueprint $table) {
            // Modifier la colonne date_vente pour inclure l'heure
            $table->dateTime('date_vente')->change();
        });
    }

    public function down()
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->date('date_vente')->change();
        });
    }
};