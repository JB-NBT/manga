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
        Schema::create('prets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preteur_id')->comment('ID de l\'utilisateur qui prête');
            $table->unsignedBigInteger('emprunteur_id')->comment('ID de l\'utilisateur qui emprunte');
            $table->unsignedBigInteger('tome_id')->comment('ID du tome prêté');
            $table->string('statut')->default('demande')->comment('Statut: demande, accepte, en_cours, restitue, refuse');
            $table->date('date_demande')->useCurrent()->comment('Date de la demande d\'emprunt');
            $table->date('date_emprunt')->nullable()->comment('Date de l\'acceptation du prêt');
            $table->date('date_retour_prevue')->nullable()->comment('Date prévue de retour');
            $table->date('date_retour_effective')->nullable()->comment('Date effective de retour');
            $table->text('motif_refus')->nullable()->comment('Motif du refus si applicable');
            $table->timestamps();

            // Clés étrangères
            $table->foreign('preteur_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('emprunteur_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tome_id')->references('id')->on('tomes')->onDelete('cascade');

            // Index
            $table->index('preteur_id');
            $table->index('emprunteur_id');
            $table->index('tome_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prets');
    }
};
