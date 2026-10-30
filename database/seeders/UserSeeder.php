<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // ✅ Ajouter
use App\Models\Division;             // ✅ Ajouter
use App\Models\Service;              // ✅ Ajouter
use App\Models\User;                 // ✅ Ajouter
use App\Models\Personnel;            // ✅ Ajouter
use App\Models\TypePersonnel;        // ✅ Ajouter si utilisé
use App\Models\Bureau; 

class UserSeeder extends Seeder
{
    
    public function run(): void
    {
        $delDiv   = Division::where('code','DEL')->first();
        $depDiv   = Division::where('code','DEP')->first();
        $dtbDiv   = Division::where('code','DTB')->first();
        $delSvc   = Service::where('division_id',$delDiv?->id)->first();
        $depSvc   = Service::where('division_id',$depDiv?->id)->first();
        $bureau1  = Bureau::where('service_id',$delSvc?->id)->first();
        $bureau2  = Bureau::where('service_id',$depSvc?->id)->first();
        $tAdmin   = TypePersonnel::where('libelle','Administrateur')->first();
        $tChef    = TypePersonnel::where('libelle','Chef de Division')->first();
        $tStag    = TypePersonnel::where('libelle','Personnel')->first();

        // ─── 1. Administrateur ──────────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@cenadi.cm'],
            ['name'=>'Administrateur CENADI','matricule'=>'ADM-001',
             'password'=>Hash::make('password')]
        );
        $adminPersonnel = Personnel::firstOrCreate(
            ['id_user' => $adminUser->id],
            ['type_personnel_id'=>$tAdmin?->id,'division_id'=>$delDiv?->id,
             'service_id'=>$delSvc?->id,'bureau_id'=>$bureau1?->id,
             'nom'=>'CENADI','prenom'=>'Administrateur',
             'statut'=>true]
        );
        $adminUser->assignRole('administrateur');

        // ─── 2. Chef DEL ────────────────────────────────────────
        $chefDelUser = User::firstOrCreate(
            ['email' => 'chef.del@cenadi.cm'],
            ['name'=>'DESSAP Jacques','matricule'=>'DEL-001',
             'password'=>Hash::make('password')]
        );
        $chefDelPersonnel = Personnel::firstOrCreate(
            ['id_user' => $chefDelUser->id],
            ['type_personnel_id'=>$tChef?->id,'division_id'=>$delDiv?->id,
             'service_id'=>$delSvc?->id,'bureau_id'=>$bureau1?->id,
             'nom'=>'DESSAP','prenom'=>'Jacques',
             'statut'=>true]
        );
        $chefDelUser->assignRole('chef_division');
        // Affecter comme chef de la division DEL
        if ($delDiv) $delDiv->update(['chef_id' => $chefDelPersonnel->id]);

        // ─── 3. Chef DEP ────────────────────────────────────────
        $chefDepUser = User::firstOrCreate(
            ['email' => 'chef.dep@cenadi.cm'],
            ['name'=>'FOPA André','matricule'=>'DEP-001',
             'password'=>Hash::make('password')]
        );
        $chefDepPersonnel = Personnel::firstOrCreate(
            ['id_user' => $chefDepUser->id],
            ['type_personnel_id'=>$tChef?->id,'division_id'=>$depDiv?->id,
             'service_id'=>$depSvc?->id,'bureau_id'=>$bureau2?->id,
             'nom'=>'FOPA','prenom'=>'André','statut'=>true]
        );
        $chefDepUser->assignRole('chef_division');
        if ($depDiv) $depDiv->update(['chef_id' => $chefDepPersonnel->id]);

        // ─── 4. Personnel standard  ─────────
        $sarbe = User::firstOrCreate(
            ['email' => 'sarbe@cenadi.cm'],
            ['name'=>'SARBE BANA Léontine Brelle','matricule'=>'24GLO56IU',
             'password'=>Hash::make('password')]
        );
        Personnel::firstOrCreate(
            ['id_user' => $sarbe->id],
            ['type_personnel_id'=>$tStag?->id,'division_id'=>$delDiv?->id,
             'service_id'=>$delSvc?->id,'bureau_id'=>$bureau1?->id,
             'nom'=>'SARBE BANA','prenom'=>'Léontine Brelle',
             'statut'=>true]
        );
        $sarbe->assignRole('personnel');

        // ─── 5. Quelques agents supplémentaires ─────────────────
        $agents = [
            ['email'=>'mbala@cenadi.cm','matricule'=>'DEL-002','name'=>'MBALA Paul',
             'nom'=>'MBALA','prenom'=>'Paul','div'=>$delDiv,'svc'=>$delSvc,'bur'=>$bureau1,'role'=>'personnel'],
            ['email'=>'nguia@cenadi.cm','matricule'=>'DEP-002','name'=>'NGUIA Marie',
             'nom'=>'NGUIA','prenom'=>'Marie','div'=>$depDiv,'svc'=>$depSvc,'bur'=>$bureau2,'role'=>'personnel'],
            ['email'=>'tamba@cenadi.cm','matricule'=>'DTB-001','name'=>'TAMBA Claude',
             'nom'=>'TAMBA','prenom'=>'Claude','div'=>$dtbDiv,'svc'=>null,'bur'=>null,'role'=>'personnel'],
        ];
        foreach ($agents as $a) {
            $u = User::firstOrCreate(
                ['email'=>$a['email']],
                ['name'=>$a['name'],'matricule'=>$a['matricule'],
                 'password'=>Hash::make('password')]
            );
            Personnel::firstOrCreate(['id_user'=>$u->id],[
                'type_personnel_id'=>$tStag?->id,
                'division_id'=>$a['div']?->id,'service_id'=>$a['svc']?->id,
                'bureau_id'=>$a['bur']?->id,'nom'=>$a['nom'],'prenom'=>$a['prenom'],
                'statut'=>true
            ]);
            $u->assignRole($a['role']);
        }

    }
}
