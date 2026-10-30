<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Ressource;
use Illuminate\Support\Facades\DB;

class RessourceApiController extends Controller
{
    /**
     * GET /api/ressources/etats
     * Pour le composant ResourceMonitor.vue
     */
    public function etats()
    {
        $ressources = Ressource::with('division')
            ->select('id','nom','etat','type_ressource','division_id')
            ->get()
            ->map(fn($r) => [
                'id'       => $r->id,
                'nom'      => $r->nom,
                'etat'     => $r->etat,
                'type'     => $r->type_ressource,
                'division' => $r->division?->code ?? '—',
            ]);

        return response()->json(['ressources' => $ressources]);
    }

    /**
     * GET /api/statistiques/graphiques
     * Pour le composant StatCharts.vue
     */
    public function statsGraphiques()
    {
        $parMois = DB::table('reservations')
            ->whereYear('created_at', now()->year)
            ->select(DB::raw('MONTH(created_at) as mois'), DB::raw('COUNT(*) as total'))
            ->groupBy('mois')->orderBy('mois')->get()
            ->pluck('total', 'mois');

        $parDivision = DB::table('reservations')
            ->join('personnels', 'reservations.personnel_id', '=', 'personnels.id')
            ->join('divisions',  'personnels.division_id',    '=', 'divisions.id')
            ->select('divisions.code', DB::raw('COUNT(*) as total'))
            ->groupBy('divisions.code')
            ->orderByDesc('total')->get()
            ->pluck('total', 'code');

        return response()->json([
            'par_mois'     => $parMois,
            'par_division' => $parDivision,
        ]);
    }
}
