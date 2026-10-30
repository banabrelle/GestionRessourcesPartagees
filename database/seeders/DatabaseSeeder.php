<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $this->call([
            PermissionSeeder::class,       // 1. Rôles & permissions Spatie
            TypePersonnelSeeder::class,    // 2. Types de personnel
            DivisionSeeder::class,         // 3. Divisions CENADI
            ServiceSeeder::class,          // 4. Services par division
            BureauSeeder::class,           // 5. Bureaux par service
            UserSeeder::class,             // 6. Users + Personnels
            TagSeeder::class,              // 7. Tags de ressources
            RessourceSeeder::class,        // 8. Ressources (matérielles + logicielles)
            ReservationSeeder::class,      // 9. Réservations de démonstration
        ]);

    }
}
