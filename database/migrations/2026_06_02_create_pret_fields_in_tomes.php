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
        Schema::table('tomes', function (Blueprint $table) {
            $table->boolean('partage')->default(false)->after('date_achat')->comment('Indique si le tome est partagé');
            $table->string('statut_pret')->default('non_partage')->after('partage')->comment('Statut du prêt: disponible, demande, prete, restitue, non_partage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tomes', function (Blueprint $table) {
            $table->dropColumn(['partage', 'statut_pret']);
        });
    }
};
