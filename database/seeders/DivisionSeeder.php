<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $divisions = [
            ['nom'=>'Division des Études et des Projets',
             'code'=>'DEP','description'=>'Études et projets de développement informatique'],
            ['nom'=>"Division de l'Exploitation et des Logiciels",
             'code'=>'DEL','description'=>'Exploitation des systèmes et maintenance logiciels'],
            ['nom'=>'Division de la Téléinformatique et de la Bureautique',
             'code'=>'DTB','description'=>'Réseaux, téléinformatique et bureautique'],
            ['nom'=>"Division de l'Informatique et de la Recherche",
             'code'=>'DIRE','description'=>'Informatique appliquée à la recherche'],
            ['nom'=>'Division des Affaires Administratives et Financières',
             'code'=>'DAAF','description'=>'Administration et finances'],
        ];
        foreach ($divisions as $d) {
            Division::firstOrCreate(['code' => $d['code']], $d);
        }

    }
}
