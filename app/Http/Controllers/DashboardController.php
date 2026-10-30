<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Ressource, Reservation, Personnel, Division, RessourceLogicielle, AiSuggestion};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $personnel = $user->personnel;

        // ══════════════════════════════════════════════════════════
        // DÉTECTER LE RÔLE
        // ══════════════════════════════════════════════════════════
        $estAdmin        = $user->hasRole('administrateur');
        $estChefDivision = $user->hasRole('chef_division') && $personnel?->division_id;
        $estPersonnel    = $user->hasRole('personnel');

        $divisionId = $estChefDivision ? $personnel->division_id : null;

        // ══════════════════════════════════════════════════════════
        // STATS — null pour le simple personnel (pas de permission)
        // ══════════════════════════════════════════════════════════
        $stats            = null;
        $licencesAlerte   = collect();
        $statsIa          = null;
        $divisionCourante = null;

        if ($estAdmin) {

            // ── Stats globales ─────────────────────────────────
            $stats = [
                'total_ressources'   => Ressource::count(),
                'disponibles'        => Ressource::disponible()->count(),
                'occupees'           => Ressource::occupe()->count(),
                'en_maintenance'     => Ressource::enMaintenance()->count(),
                'total_reservations' => Reservation::count(),
                'en_attente'         => Reservation::enAttente()->count(),
                'valide_chef'        => Reservation::where('statut', 'valide_chef')->count(),
                'valide_admin'       => Reservation::valides()->count(),
                'rejetes'            => Reservation::rejetes()->count(),
                'total_personnels'   => Personnel::count(),
                'total_divisions'    => Division::count(),
                'conflits'           => Reservation::where('conflit_detecte', true)->count(),
                'licences_alerte'    => RessourceLogicielle::whereNotNull('date_fin_licence')
                                            ->where('date_fin_licence', '>=', now())
                                            ->where('date_fin_licence', '<=', now()->addDays(30))
                                            ->count(),
            ];

            $licencesAlerte = RessourceLogicielle::with('ressource')
                ->whereNotNull('date_fin_licence')
                ->where('date_fin_licence', '>=', now())
                ->where('date_fin_licence', '<=', now()->addDays(30))
                ->get();

            $totalIa     = AiSuggestion::count();
            $accepteesIa = AiSuggestion::where('acceptee', true)->count();
            $statsIa     = [
                'total'            => $totalIa,
                'acceptees'        => $accepteesIa,
                'taux_acceptation' => $totalIa > 0 ? round($accepteesIa / $totalIa * 100) : 0,
            ];

        } elseif ($estChefDivision) {

            // ── Stats division uniquement ──────────────────────
            $personnelsDivision = Personnel::where('division_id', $divisionId)->pluck('id');
            $ressourcesDivision = Ressource::where('division_id', $divisionId)->pluck('id');

            $stats = [
                'total_ressources'   => Ressource::where('division_id', $divisionId)->count(),
                'disponibles'        => Ressource::where('division_id', $divisionId)->disponible()->count(),
                'occupees'           => Ressource::where('division_id', $divisionId)->occupe()->count(),
                'en_maintenance'     => Ressource::where('division_id', $divisionId)->enMaintenance()->count(),
                'total_reservations' => Reservation::whereIn('personnel_id', $personnelsDivision)->count(),
                'en_attente'         => Reservation::whereIn('personnel_id', $personnelsDivision)->enAttente()->count(),
                'valide_chef'        => Reservation::whereIn('personnel_id', $personnelsDivision)->where('statut', 'valide_chef')->count(),
                'valide_admin'       => Reservation::whereIn('personnel_id', $personnelsDivision)->valides()->count(),
                'rejetes'            => Reservation::whereIn('personnel_id', $personnelsDivision)->rejetes()->count(),
                'total_personnels'   => $personnelsDivision->count(),
                'total_divisions'    => 1,
                'conflits'           => Reservation::whereIn('personnel_id', $personnelsDivision)
                                            ->where('conflit_detecte', true)->count(),
                'licences_alerte'    => RessourceLogicielle::whereIn('ressource_id', $ressourcesDivision)
                                            ->whereNotNull('date_fin_licence')
                                            ->where('date_fin_licence', '>=', now())
                                            ->where('date_fin_licence', '<=', now()->addDays(30))
                                            ->count(),
            ];

            $licencesAlerte = RessourceLogicielle::with('ressource')
                ->whereHas('ressource', fn($q) => $q->where('division_id', $divisionId))
                ->whereNotNull('date_fin_licence')
                ->where('date_fin_licence', '>=', now())
                ->where('date_fin_licence', '<=', now()->addDays(30))
                ->get();

            $iaQuery     = AiSuggestion::whereHas('reservation', fn($q) =>
                                $q->whereHas('personnel', fn($q2) =>
                                    $q2->where('division_id', $divisionId)));
            $totalIa     = $iaQuery->count();
            $accepteesIa = (clone $iaQuery)->where('acceptee', true)->count();
            $statsIa     = [
                'total'            => $totalIa,
                'acceptees'        => $accepteesIa,
                'taux_acceptation' => $totalIa > 0 ? round($accepteesIa / $totalIa * 100) : 0,
            ];

            $divisionCourante = Division::find($divisionId);
        }

        // simple personnel → $stats, $statsIa, $licencesAlerte, $divisionCourante restent null/vides

        // Taux d'utilisation — calculé seulement si on a des stats
        if ($stats !== null) {
            $stats['taux_utilisation'] = $stats['total_ressources'] > 0
                ? round(($stats['occupees'] / $stats['total_ressources']) * 100)
                : 0;
        }

        // ══════════════════════════════════════════════════════════
        // RÉSERVATIONS RÉCENTES — tous les rôles voient les leurs
        // ══════════════════════════════════════════════════════════
        $query = Reservation::with(['personnel.user', 'ressource'])->orderByDesc('created_at');

        if ($estPersonnel && $personnel) {
            // Simple personnel : uniquement ses propres réservations
            $query->where('personnel_id', $personnel->id);
        } elseif ($estChefDivision) {
            // Chef : réservations de sa division
            $query->whereHas('personnel', fn($q) => $q->where('division_id', $divisionId));
        }
        // Admin : aucun filtre → tout

        $reservationsRecentes = $query->take(8)->get();

        return view('dashboard.index', compact(
            'stats',               // null pour simple personnel
            'reservationsRecentes',
            'licencesAlerte',
            'statsIa',             // null pour simple personnel
            'divisionCourante',    // null sauf chef_division
            'estPersonnel',
            'estChefDivision',
            'estAdmin'
        ));
    }
}