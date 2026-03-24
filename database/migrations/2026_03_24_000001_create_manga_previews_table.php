<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manga_previews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manga_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('ordre')->comment('1 ou 2 - numéro de page preview');
            $table->string('image_path');
            $table->timestamps();
            $table->unique(['manga_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manga_previews');
    }
};
