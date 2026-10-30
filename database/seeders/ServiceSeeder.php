<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division; 
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $del = Division::where('code','DEL')->first();
        $dep = Division::where('code','DEP')->first();
        $dtb = Division::where('code','DTB')->first();
        $daaf = Division::where('code','DAAF')->first();

        $services = [
            // Services DEL
            ['division_id'=>$del?->id,'nom'=>'Service Exploitation',
             'description'=>'Exploitation des serveurs et applications'],
            ['division_id'=>$del?->id,'nom'=>'Service Maintenance Logiciels',
             'description'=>'Maintenance et mises à jour logiciels'],
            ['division_id'=>$del?->id,'nom'=>'Service Développement',
             'description'=>'Développement de nouvelles applications'],
            // Services DEP
            ['division_id'=>$dep?->id,'nom'=>'Service Architecture',
             'description'=>'Architecture des systèmes d information'],
            // Services DTB
            ['division_id'=>$dtb?->id,'nom'=>'Service Réseaux',
             'description'=>'Administration des réseaux et télécom'],
            ['division_id'=>$dtb?->id,'nom'=>'Service Bureautique',
             'description'=>'Support bureautique et formation'],
            // Services DAAF
            ['division_id'=>$daaf?->id,'nom'=>'Service Comptabilité',
             'description'=>'Gestion comptable et financière'],
            ['division_id'=>$daaf?->id,'nom'=>'Service Ressources Humaines',
             'description'=>'Gestion du personnel et paie'],
        ];
        foreach ($services as $s) {
            if ($s['division_id']) Service::firstOrCreate(['nom'=>$s['nom'],'division_id'=>$s['division_id']],$s);
        }

    }
}
