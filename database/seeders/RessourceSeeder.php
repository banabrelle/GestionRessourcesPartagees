<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division;
use App\Models\Ressource;
use App\Models\RessourceMaterielle; // ✅ DOIT être présent
use App\Models\Tag;  // ✅ Ajouter si utilisé
use App\Models\RessourceLogicielle;   // ✅ Ajouter si utilisé

class RessourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $del = Division::where('code','DEL')->first();
        $dep = Division::where('code','DEP')->first();
        $dtb = Division::where('code','DTB')->first();

        // ─── RESSOURCES MATÉRIELLES ─────────────────────────────
        $materielsDef = [
            ['nom'=>'Vidéoprojecteur Epson EB-X41','ns'=>'VP-EP-001','div'=>$del,
             'tags'=>['vidéoprojecteur','présentation','salle de réunion'],
             'mat'=>['marque'=>'Epson','modele'=>'EB-X41','valeur_achat'=>450000,
                     'date_acquisition'=>'2022-06-15']],
            ['nom'=>'Laptop Dell Latitude 5520','ns'=>'LAP-DL-001','div'=>$del,
             'tags'=>['ordinateur','portable','travail mobile'],
             'mat'=>['marque'=>'Dell','modele'=>'Latitude 5520','valeur_achat'=>850000,
                     'date_acquisition'=>'2023-01-10','capacite'=>'16Go RAM / 512Go SSD']],
            ['nom'=>'Serveur HP ProLiant DL360','ns'=>'SRV-HP-001','div'=>$dep,
             'tags'=>['serveur','réseau'],
             'mat'=>['marque'=>'HP','modele'=>'ProLiant DL360 G10','valeur_achat'=>3500000,
                     'date_acquisition'=>'2021-03-20','capacite'=>'64Go RAM / 2To']],
            ['nom'=>'Imprimante HP LaserJet M428','ns'=>'IMP-HP-001','div'=>$daaf ?? $del,
             'tags'=>['imprimante'],
             'mat'=>['marque'=>'HP','modele'=>'LaserJet M428fdn','valeur_achat'=>350000,
                     'date_acquisition'=>'2023-05-01']],
            ['nom'=>'Onduleur APC Smart-UPS 1500','ns'=>'UPS-APC-001','div'=>$del,
             'tags'=>['onduleur','serveur'],
             'mat'=>['marque'=>'APC','modele'=>'Smart-UPS 1500VA','valeur_achat'=>280000,
                     'date_acquisition'=>'2022-11-10']],
        ];

        foreach ($materielsDef as $def) {
            $r = Ressource::firstOrCreate(['numero_serie'=>$def['ns']],[
                'division_id'    => $def['div']?->id,
                'type_ressource' => 'materiel',
                'etat'           => 'disponible',
                'mode_partage'   => 'exclusif',
                'nom'            => $def['nom'],
                'numero_serie'   => $def['ns'],
            ]);
            RessourceMaterielle::firstOrCreate(['ressource_id'=>$r->id],$def['mat']);
            // Attacher les tags
            $tagIds = Tag::whereIn('libelle',$def['tags'])->pluck('id');
            $r->tags()->syncWithoutDetaching($tagIds);
        }

        // ─── RESSOURCES LOGICIELLES ─────────────────────────────
        $logicielsDef = [
            ['nom'=>'Microsoft Office 365','ns'=>'LOG-MS365-001','div'=>$del,
             'tags'=>['bureautique'],
             'log'=>['categorie'=>'Bureautique','type_licence'=>'abonnement',
                     'nombre_licence'=>50,'cles_utilisees'=>35,'editeur'=>'Microsoft',
                     'version'=>'2024','date_debut_licence'=>'2024-01-01',
                     'date_fin_licence'=>'2026-12-31']],
            ['nom'=>'Oracle Database 19c','ns'=>'LOG-ORA-001','div'=>$dep,
             'tags'=>['SGBD','développement'],
             'log'=>['categorie'=>'SGBD','type_licence'=>'proprietaire',
                     'nombre_licence'=>10,'cles_utilisees'=>8,'editeur'=>'Oracle',
                     'version'=>'19c','date_debut_licence'=>'2023-03-01',
                     'date_fin_licence'=>'2026-02-28']],
            ['nom'=>'Kaspersky Endpoint Security','ns'=>'LOG-KAS-001','div'=>$del,
             'tags'=>['antivirus','sécurité'],
             'log'=>['categorie'=>'Sécurité','type_licence'=>'abonnement',
                     'nombre_licence'=>100,'cles_utilisees'=>87,'editeur'=>'Kaspersky',
                     'version'=>'12.0','date_debut_licence'=>'2025-01-01',
                     'date_fin_licence'=>'2026-01-31']],
        ];

        foreach ($logicielsDef as $def) {
            $r = Ressource::firstOrCreate(['numero_serie'=>$def['ns']],[
                'division_id'    => $def['div']?->id,
                'type_ressource' => 'logiciel',
                'etat'           => 'disponible',
                'mode_partage'   => 'partage',
                'nom'            => $def['nom'],
                'numero_serie'   => $def['ns'],
            ]);
            RessourceLogicielle::firstOrCreate(['ressource_id'=>$r->id],$def['log']);
            $tagIds = Tag::whereIn('libelle',$def['tags'])->pluck('id');
            $r->tags()->syncWithoutDetaching($tagIds);
        }

    }
}
