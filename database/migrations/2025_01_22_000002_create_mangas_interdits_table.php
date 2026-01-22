<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mangas_interdits', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 255);
            $table->string('auteur', 255)->nullable();
            $table->text('raison');
            $table->foreignId('ajoute_par')->constrained('users')->onDelete('cascade');
            $table->date('date_interdiction')->useCurrent();
            $table->timestamps();

            // Index pour recherche rapide
            $table->index('titre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mangas_interdits');
    }
};
