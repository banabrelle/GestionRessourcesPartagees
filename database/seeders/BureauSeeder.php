<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service; // 
use App\Models\Bureau;  // 

class BureauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
               Service::all()->each(function($service) {
            // Créer 2 bureaux par service
            for ($i = 1; $i <= 2; $i++) {
                Bureau::firstOrCreate([
                    'service_id' => $service->id,
                    'nom'        => 'Bureau ' . strtoupper(substr($service->nom,0,3)) . '-0' . $i,
                ], [
                    'description' => 'Bureau N°' . $i . ' du ' . $service->nom,
                ]);
            }
        });

    }
}
