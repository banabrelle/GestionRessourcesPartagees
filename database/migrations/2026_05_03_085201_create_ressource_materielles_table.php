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
        Schema::create('ressource_materielles', function (Blueprint $table) {
            $table->id();
                        // Héritage 1:1 avec la table parente ressources
            $table->unsignedBigInteger('ressource_id')->unique();
            // Attributs spécifiques — classe Ressources matérielles (UML)
            $table->string('marque', 100)->nullable();
            $table->string('modele', 100)->nullable();
            $table->date('date_acquisition')->nullable();
            $table->string('capacite', 100)->nullable();
            $table->decimal('valeur_achat', 15, 2)->nullable();
            $table->foreign('ressource_id')
                  ->references('id')->on('ressources')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ressource_materielles');
    }
};
