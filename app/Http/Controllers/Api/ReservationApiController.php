<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Reservation;

class ReservationApiController extends Controller
{
    /**
     * GET /api/reservations/calendrier
     * Pour le composant CalendarView.vue
     */
    public function calendrier()
    {
        $user  = auth()->user();
        $perso = $user->personnel;

        $query = Reservation::with(['personnel','ressource'])
            ->where('date_debut', '>=', now()->startOfMonth()->subMonth())
            ->where('date_fin',   '<=', now()->endOfMonth()->addMonth());

        if ($user->hasRole('personnel') && $perso) {
            $query->where('personnel_id', $perso->id);
        } elseif ($user->hasRole('chef_division') && $perso) {
            $query->whereHas('personnel',
                fn($q) => $q->where('division_id', $perso->division_id));
        }

        $reservations = $query->get()->map(fn($r) => [
            'id'         => $r->id,
            'ressource'  => $r->ressource->nom,
            'personnel'  => $r->personnel->nom_complet,
            'date_debut' => $r->date_debut->format('Y-m-d H:i'),
            'date_fin'   => $r->date_fin->format('Y-m-d H:i'),
            'statut'     => $r->statut,
        ]);

        return response()->json(['reservations' => $reservations]);
    }
}

