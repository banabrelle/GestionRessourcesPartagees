<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reservation;       // ✅ Ajouter
use App\Models\HistoriqueEmprunt;  // ✅ Ajouter
use App\Models\User;               // ✅ Ajouter si utilisé
use App\Models\Personnel;          // ✅ Ajouter si utilisé
use App\Models\Ressource;          // ✅ Ajouter si utilisé

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                $personnel = Personnel::with('user')->whereHas('user', fn($q)=>$q->where('matricule','24GLO56IU'))->first();
        $chef      = Personnel::with('user')->whereHas('user', fn($q)=>$q->where('matricule','DEL-001'))->first();
        $admin     = Personnel::with('user')->whereHas('user', fn($q)=>$q->where('matricule','ADM-001'))->first();
        $vp        = Ressource::where('numero_serie','VP-EP-001')->first();
        $laptop    = Ressource::where('numero_serie','LAP-DL-001')->first();

        if (!$personnel || !$vp) return;

        // Réservation validée (avec bon d'emprunt)
        $r1 = Reservation::create([
            'personnel_id'   => $personnel->id,
            'ressource_id'   => $vp->id,
            'date_debut'     => now()->subDays(5)->setHour(9),
            'date_fin'       => now()->subDays(5)->setHour(12),
            'motif'          => 'Présentation du projet GERES-CENADI à la direction',
            'statut'         => 'valide_admin',
            'valide_par'     => $chef?->id,
            'valide_par_admin'=> $admin?->id,
            'date_val_chef'  => now()->subDays(6),
            'date_val_admin' => now()->subDays(5),
        ]);
        HistoriqueEmprunt::create([
            'reservation_id'    => $r1->id,
            'numero_bon'        => HistoriqueEmprunt::genererNumeroBon(),
            'date_retour_prevue'=> now()->subDays(5)->setHour(12),
            'date_retour_relle' => now()->subDays(5)->setHour(12)->addMinutes(15),
            'etat_retour'       => 'rendu_bon_etat',
        ]);

        // Réservation en attente
        if ($laptop) {
            Reservation::create([
                'personnel_id' => $personnel->id,
                'ressource_id' => $laptop->id,
                'date_debut'   => now()->addDays(2)->setHour(8),
                'date_fin'     => now()->addDays(4)->setHour(18),
                'motif'        => 'Travail hors site — déplacement formation Laravel',
                'statut'       => 'en_attente',
            ]);
        }

    }
}
