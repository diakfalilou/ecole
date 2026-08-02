<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tblnote_temp', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_id')->index(); // regroupe les lignes d'un même import
            $table->unsignedBigInteger('ecole_id')->index();
            $table->string('annee_scolaire', 20)->index();
            $table->integer('niveau_id');
            $table->integer('classe_id')->index();
            $table->unsignedBigInteger('matiere_id')->index();
            $table->enum('periode_type', ['trimestre', 'semestre']);
            $table->tinyInteger('periode_numero');
            $table->enum('type', ['cours', 'compo'])->default('cours');
            $table->string('mois', 20)->nullable();

            $table->unsignedBigInteger('eleve_id')->nullable()->index(); // null si non trouvé
            $table->string('nom_extrait', 255)->nullable(); // nom brut lu dans le fichier
            $table->decimal('note', 5, 2)->nullable();
            $table->enum('statut_match', ['exact', 'probable', 'non_trouve'])->default('non_trouve');

            $table->string('fichier_origine', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblnote_temp');
    }
};
