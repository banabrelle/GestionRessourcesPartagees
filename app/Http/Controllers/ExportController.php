<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ← AJOUTER
use App\Models\{Ressource, Reservation, Personnel, Division, RessourceLogicielle, AiSuggestion};

class ExportController extends Controller
{
    use AuthorizesRequests; 
    // ════════════════════════════════════════════════════════════
    // EXPORT STATISTIQUES GLOBAL
    // Route : GET /export/statistiques-pdf
    // Permission : exporter rapports
    // ════════════════════════════════════════════════════════════
    public function statistiquesPdf()
    {
        $this->authorize('exporter rapports'); // ou middleware dans routes

        // ── Stats globales ─────────────────────────────────────
        $stats = $this->calcStatsGlobales();

        // ── Stats par division (tableau récapitulatif) ─────────
        $statsDivisions = Division::withCount([
            'ressources as total_ressources',
            'ressources as ressources_disponibles' => fn($q) => $q->where('etat', 'disponible'),
            'ressources as ressources_occupees'    => fn($q) => $q->where('etat', 'occupe'),
            'personnels as total_personnels',
        ])
        ->withCount([
            'personnels as total_reservations' => fn($q) =>
                $q->join('reservations', 'reservations.personnel_id', '=', 'personnels.id')
                  ->select(\DB::raw('count(reservations.id)')),
            'personnels as reservations_attente' => fn($q) =>
                $q->join('reservations', 'reservations.personnel_id', '=', 'personnels.id')
                  ->where('reservations.statut', 'en_attente')
                  ->select(\DB::raw('count(reservations.id)')),
        ])
        ->get();

        // ── Licences en alerte globales ────────────────────────
        $licencesAlerte = RessourceLogicielle::with('ressource.division')
            ->whereNotNull('date_fin_licence')
            ->where('date_fin_licence', '>=', now())
            ->where('date_fin_licence', '<=', now()->addDays(30))
            ->orderBy('date_fin_licence')
            ->get();

        $pdf = Pdf::loadView('exports.statistiques-pdf', [
            'stats'          => $stats,
            'statsDivisions' => $statsDivisions,
            'licencesAlerte' => $licencesAlerte,
            'date_export'    => now()->format('d/m/Y H:i'),
            'genere_par'     => auth()->user()->name,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('statistiques-global-' . date('Y-m-d') . '.pdf');
    }

    // ════════════════════════════════════════════════════════════
    // EXPORT STATISTIQUES PAR DIVISION
    // Route : GET /export/statistiques-division/{division}
    // Permission : exporter rapports  OU  voir statistiques division
    // ════════════════════════════════════════════════════════════
    public function statistiquesDivisionPdf(Division $division)
    {
        // Un chef_division ne peut exporter QUE sa propre division
        $user      = auth()->user();
        $personnel = $user->personnel;

        if ($user->hasRole('chef_division') && $personnel?->division_id !== $division->id) {
            abort(403, 'Vous ne pouvez exporter que les statistiques de votre division.');
        }

        // ── Stats filtrées ─────────────────────────────────────
        $personnelsDivision = Personnel::where('division_id', $division->id)->pluck('id');
        $ressourcesDivision = Ressource::where('division_id', $division->id)->pluck('id');

        $stats = [
            'total_ressources'   => Ressource::where('division_id', $division->id)->count(),
            'disponibles'        => Ressource::where('division_id', $division->id)->disponible()->count(),
            'occupees'           => Ressource::where('division_id', $division->id)->occupe()->count(),
            'en_maintenance'     => Ressource::where('division_id', $division->id)->enMaintenance()->count(),
            'total_reservations' => Reservation::whereIn('personnel_id', $personnelsDivision)->count(),
            'en_attente'         => Reservation::whereIn('personnel_id', $personnelsDivision)->enAttente()->count(),
            'valide_chef'        => Reservation::whereIn('personnel_id', $personnelsDivision)->where('statut', 'valide_chef')->count(),
            'valide_admin'       => Reservation::whereIn('personnel_id', $personnelsDivision)->valides()->count(),
            'rejetes'            => Reservation::whereIn('personnel_id', $personnelsDivision)->rejetes()->count(),
            'total_personnels'   => $personnelsDivision->count(),
            'total_divisions'    => 1,
            'conflits'           => Reservation::whereIn('personnel_id', $personnelsDivision)->where('conflit_detecte', true)->count(),
            'licences_alerte'    => RessourceLogicielle::whereIn('ressource_id', $ressourcesDivision)
                                        ->whereNotNull('date_fin_licence')
                                        ->where('date_fin_licence', '>=', now())
                                        ->where('date_fin_licence', '<=', now()->addDays(30))
                                        ->count(),
        ];

        $stats['taux_utilisation'] = $stats['total_ressources'] > 0
            ? round(($stats['occupees'] / $stats['total_ressources']) * 100)
            : 0;

        // ── Ressources de la division ──────────────────────────
        $ressourcesDivisionList = Ressource::with(['ficheMaterielle', 'ficheLogicielle'])
            ->where('division_id', $division->id)
            ->orderBy('nom')
            ->get();

        // ── Licences en alerte de la division ─────────────────
        $licencesAlerte = RessourceLogicielle::with('ressource')
            ->whereIn('ressource_id', $ressourcesDivision)
            ->whereNotNull('date_fin_licence')
            ->where('date_fin_licence', '>=', now())
            ->where('date_fin_licence', '<=', now()->addDays(30))
            ->orderBy('date_fin_licence')
            ->get();

        $pdf = Pdf::loadView('exports.statistiques-division-pdf', [
            'stats'               => $stats,
            'division'            => $division,
            'ressourcesDivision'  => $ressourcesDivisionList,
            'licencesAlerte'      => $licencesAlerte,
            'date_export'         => now()->format('d/m/Y H:i'),
            'genere_par'          => auth()->user()->name,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('statistiques-' . $division->code . '-' . date('Y-m-d') . '.pdf');
    }

    // ════════════════════════════════════════════════════════════
    // MÉTHODE PRIVÉE RÉUTILISABLE — stats globales
    // ════════════════════════════════════════════════════════════
    private function calcStatsGlobales(): array
    {
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

        $stats['taux_utilisation'] = $stats['total_ressources'] > 0
            ? round(($stats['occupees'] / $stats['total_ressources']) * 100)
            : 0;

        return $stats;
    }
}