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
        Schema::create('ressource_logicielles', function (Blueprint $table) {
            $table->id();
                        // Héritage 1:1 avec la table parente ressources
            $table->unsignedBigInteger('ressource_id')->unique();
            // Attributs spécifiques — classe Ressources logicielles (UML)
            $table->string('categorie', 150)->nullable();
            $table->enum('type_licence', [
                'proprietaire', 'libre', 'freemium', 'abonnement'
            ])->default('proprietaire');
            $table->unsignedInteger('nombre_licence')->default(1);
            // Contrainte UML : {nombre_licence >= réservations actives}
            // Vérifiée dans le service ConflictDetectionService
            $table->unsignedInteger('cles_utilisees')->default(0);
            $table->string('editeur', 150)->nullable();
            $table->string('version', 50)->nullable();
            $table->string('cle_produit')->nullable();
            $table->date('date_debut_licence')->nullable();
            $table->date('date_fin_licence')->nullable();
            $table->boolean('alerte_envoyee')->default(false);
            $table->timestamps();

            $table->foreign('ressource_id')
                  ->references('id')->on('ressources')
                  ->onDelete('cascade');
            // Index pour les alertes d'expiration
            $table->index('date_fin_licence');

           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ressource_logicielles');
    }
};
