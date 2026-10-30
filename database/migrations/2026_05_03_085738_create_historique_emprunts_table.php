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
        Schema::create('historique_emprunts', function (Blueprint $table) {
            $table->id();
                        // Relation UML: concerne (Reservations -> historique_Emprunt)
            $table->unsignedBigInteger('reservation_id');
            $table->string('numero_bon', 30)->unique();
            // Attributs UML: date_retour_prevue, date_retour_relle, etat_retour
            $table->dateTime('date_retour_prevue')->nullable();
            $table->dateTime('date_retour_relle')->nullable();
            $table->enum('etat_retour', [
                'non_rendu',
                'rendu_bon_etat',
                'rendu_endommage',
                'perdu'
            ])->default('non_rendu');
            $table->string('fichier_pdf')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('reservation_id')
                  ->references('id')->on('reservations')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_emprunts');
    }
};
