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
        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('personnel_id');
            $table->unsignedBigInteger('reservation_id')->nullable();
            // Type de suggestion: creneau (moteur temporel) ou ressource (moteur substitution)
            $table->enum('type_suggestion', ['creneau', 'ressource','conflit']);
            $table->json('suggestion_data'); // données JSON de la suggestion
            $table->boolean('acceptee')->default(false);
            $table->boolean('ignoree')->default(false);
            $table->unsignedInteger('temps_gagne_minutes')->nullable();

            $table->foreign('personnel_id')
                  ->references('id')->on('personnels')
                  ->onDelete('cascade');
            $table->foreign('reservation_id')
                  ->references('id')->on('reservations')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_suggestions');
    }
};
