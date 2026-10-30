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
        Schema::create('ressources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id')->nullable();
            // Attributs du diagramme UML — classe Ressources
            $table->enum('type_ressource', ['materiel', 'logiciel']);
            $table->enum('etat', [
                'disponible',
                'occupe',
                'en_attente_validation',
                'en_maintenance',
                'expire'
            ])->default('disponible');
            $table->enum('mode_partage', [
                'exclusif',
                'partage',
                'reserve'
            ])->default('exclusif');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('numero_serie', 100)->nullable()->unique();

            $table->softDeletes(); // suppression logique

            $table->foreign('division_id')
                  ->references('id')->on('divisions')
                  ->onDelete('set null');
            // Index sur etat pour les filtres
            $table->index('etat');
            $table->index('type_ressource');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ressources');
    }
};
