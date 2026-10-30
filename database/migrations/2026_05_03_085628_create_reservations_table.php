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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
                        // Classe Personnel demande 0..* Reservations
            $table->unsignedBigInteger('personnel_id');
            // Ressource Concerne 0..* Reservations
            $table->unsignedBigInteger('ressource_id');
            // Attributs UML: date_début, date_fin, motif, validé_par
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            // Contrainte UML: {date_fin > date_debut} vérifiée dans Request
            // Contrainte UML: {no overlap} vérifiée dans ConflictDetectionService
            $table->text('motif');
            $table->enum('statut', [
                'en_attente',
                'valide_chef',
                'valide_admin',
                'rejete_chef',
                'rejete_admin',
                'annule',
                'termine'
            ])->default('en_attente');
            // Chef qui valide (Valide 0..* Reservations)
            $table->unsignedBigInteger('valide_par')->nullable();
            $table->unsignedBigInteger('valide_par_admin')->nullable();
            $table->dateTime('date_val_chef')->nullable();
            $table->dateTime('date_val_admin')->nullable();
            $table->text('commentaire_chef')->nullable();
            $table->text('commentaire_admin')->nullable();
            $table->boolean('conflit_detecte')->default(false);

            $table->foreign('personnel_id')
                  ->references('id')->on('personnels')
                  ->onDelete('cascade');
            $table->foreign('ressource_id')
                  ->references('id')->on('ressources')
                  ->onDelete('cascade');
            $table->foreign('valide_par')
                  ->references('id')->on('personnels')
                  ->onDelete('set null');
            $table->foreign('valide_par_admin')
                  ->references('id')->on('personnels')
                  ->onDelete('set null');
            // Index pour la détection de conflits (performance)
            $table->index(['ressource_id', 'date_debut', 'date_fin']);
            $table->index('statut');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
