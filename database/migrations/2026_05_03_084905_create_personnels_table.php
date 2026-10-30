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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user')->unique();
            // Clés étrangères du diagramme UML
            $table->unsignedBigInteger('type_personnel_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('bureau_id')->nullable();
            // Attributs de la classe Personnel (UML)
            $table->string('nom', 150);
            $table->string('prenom', 150);
            $table->boolean('statut')->default(true);
                        // Contraintes de clés étrangères
            $table->foreign('id_user')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('type_personnel_id')
                  ->references('id')->on('type_personnels')
                  ->onDelete('set null');
            $table->foreign('division_id')
                  ->references('id')->on('divisions')
                  ->onDelete('set null');
            $table->foreign('service_id')
                  ->references('id')->on('services')
                  ->onDelete('set null');
            $table->foreign('bureau_id')
                  ->references('id')->on('bureaus')
                  ->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
