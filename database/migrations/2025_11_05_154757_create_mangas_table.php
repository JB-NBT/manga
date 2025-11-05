<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mangas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('titre');
            $table->string('auteur');
            $table->text('description')->nullable();
            $table->string('image_couverture')->nullable();
            $table->integer('nombre_tomes')->default(1);
            $table->enum('statut', ['en_cours', 'termine', 'abandonne'])->default('en_cours');
            $table->integer('note')->nullable()->comment('Note sur 10');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mangas');
    }
};
