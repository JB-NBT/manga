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
        // Ajouter url_lecture à la table mangas (pour la page index du site)
        Schema::table('mangas', function (Blueprint $table) {
            $table->string('url_lecture_index')->nullable()->after('est_public');
        });

        // Ajouter url_lecture à la table tomes (pour chaque tome spécifique)
        Schema::table('tomes', function (Blueprint $table) {
            $table->string('url_lecture')->nullable()->after('date_achat');
        });

        // Ajouter message_utilisateur à publication_requests si pas déjà présent
        if (!Schema::hasColumn('publication_requests', 'message_utilisateur')) {
            Schema::table('publication_requests', function (Blueprint $table) {
                $table->text('message_utilisateur')->nullable()->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mangas', function (Blueprint $table) {
            $table->dropColumn('url_lecture_index');
        });

        Schema::table('tomes', function (Blueprint $table) {
            $table->dropColumn('url_lecture');
        });

        if (Schema::hasColumn('publication_requests', 'message_utilisateur')) {
            Schema::table('publication_requests', function (Blueprint $table) {
                $table->dropColumn('message_utilisateur');
            });
        }
    }
};
