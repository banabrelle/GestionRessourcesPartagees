<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypePersonnel;

class TypePersonnelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $types = [
            ['libelle' => 'Personnel',      'description' => 'Employé au cenadi'],
            ['libelle' => 'Chef de Division',       'description' => 'Responsable de division'],
            ['libelle' => 'Administrateur',        'description' => 'Administrateur système'],
        ];
        foreach ($types as $t) {
            TypePersonnel::firstOrCreate(['libelle' => $t['libelle']], $t);
        }

    }
}
