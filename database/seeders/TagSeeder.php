<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag; // 


class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Tags matériel
            'ordinateur','portable','serveur','imprimante','vidéoprojecteur',
            'onduleur','routeur','switch','scanner','webcam',
            'salle de réunion','présentation','travail mobile','réseau',
            // Tags logiciel
            'bureautique','SGBD','sécurité','développement','système',
            'antivirus','sauvegarde','comptabilité','design',
        ];
        foreach ($tags as $t) {
            Tag::firstOrCreate(['libelle' => $t]);
        }

    }
}
