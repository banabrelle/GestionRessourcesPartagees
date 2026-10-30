<?php

namespace App\Http\Controllers;

use App\Models\{AiSuggestion, Reservation, Ressource};
use App\Services\{AiSuggestionService, ConflictDetectionService};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiSuggestionController extends Controller
{
    public function __construct(
        private AiSuggestionService      $aiSvc,
        private ConflictDetectionService $conflictSvc
    ) {}

    
    // VUES
    
    public function index(Request $request)
    {
        $query = AiSuggestion::with(['personnel.user', 'reservation.ressource'])
                              ->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type_suggestion', $request->type);
        }

        if ($request->filled('statut')) {
            match ($request->statut) {
                'acceptee'   => $query->where('acceptee', true),
                'ignoree'    => $query->where('ignoree', true),
                'en_attente' => $query->where('acceptee', false)->where('ignoree', false),
                default      => null,
            };
        }

        $suggestions   = $query->paginate(20)->withQueryString();
        $stats         = $this->aiSvc->statistiques();
        $statsConflits = $this->conflictSvc->statistiquesConflits();

        $parType = AiSuggestion::select(
                'type_suggestion',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(acceptee) as acceptees')
            )
            ->groupBy('type_suggestion')
            ->get();

        return view('ai.index', compact(
            'suggestions', 'stats', 'statsConflits', 'parType'
        ));
    }

    public function show(AiSuggestion $suggestion)
    {
        $suggestion->load(['personnel.user', 'reservation.ressource']);
        return view('ai.show', compact('suggestion'));
    }

    public function accepter(AiSuggestion $suggestion)
    {
        $suggestion->update([
            'acceptee'            => true,
            'ignoree'             => false,
            'temps_gagne_minutes' => 15,
        ]);
        return back()->with('success', 'Suggestion marquée comme acceptée.');
    }

    public function ignorer(AiSuggestion $suggestion)
    {
        $suggestion->update(['ignoree' => true]);
        return back()->with('success', 'Suggestion ignorée.');
    }

    
    // AJAX — appelées par le JavaScript (fetch JSON)
    

   
    public function verifierConflit(Request $request)
    {
        $data = $request->validate([
            'ressource_id' => 'required|exists:ressources,id',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after:date_debut',
        ]);

        // ✅ STATUTS CORRECTS — identiques à ceux du ReservationController
        $statutsActifs = ['en_attente', 'valide_chef', 'valide_admin'];

        $ressource = Ressource::findOrFail($data['ressource_id']);

        // Cas logiciel : conflit seulement si toutes les licences sont prises
        if ($ressource->type_ressource === 'logiciel' && $ressource->ficheLogicielle) {
            $nbActives = Reservation::where('ressource_id', $data['ressource_id'])
                ->whereIn('statut', $statutsActifs)
                ->where('date_debut', '<', $data['date_fin'])
                ->where('date_fin',   '>', $data['date_debut'])
                ->count();

            $conflit = $nbActives >= $ressource->ficheLogicielle->nombre_licence;
        } else {
            // Cas matériel : conflit si chevauchement
            $conflit = Reservation::where('ressource_id', $data['ressource_id'])
                ->whereIn('statut', $statutsActifs)
                ->where('date_debut', '<', $data['date_fin'])
                ->where('date_fin',   '>', $data['date_debut'])
                ->exists();
        }

        // Journaliser uniquement si conflit réel détecté
        if ($conflit && auth()->user()->personnel) {
            AiSuggestion::create([
                'personnel_id'    => auth()->user()->personnel->id,
                'reservation_id'  => null,
                'type_suggestion' => 'conflit',
                'suggestion_data' => [
                    'ressource_id' => $data['ressource_id'],
                    'ressource'    => $ressource->nom,
                    'date_debut'   => $data['date_debut'],
                    'date_fin'     => $data['date_fin'],
                ],
                'acceptee' => false,
                'ignoree'  => false,
            ]);
        }

        return response()->json([
            'conflit'    => $conflit,
            'ressource'  => $ressource->nom,
            'etat'       => $ressource->etat,
        ]);
    }

    /**
     * POST /ajax/ia/suggerer-creneaux
     */
    public function suggererCreneaux(Request $request)
    {
        $data = $request->validate([
            'ressource_id' => 'required|exists:ressources,id',
            'duree_heures' => 'required|integer|min:1|max:72',
            'n'            => 'nullable|integer|min:1|max:6',
        ]);

        $duree = (int) $data['duree_heures'];
        $n     = (int) ($data['n'] ?? 3);

        // ✅ STATUTS CORRECTS
        $statutsActifs = ['en_attente', 'valide_chef', 'valide_admin'];

        $reservations = Reservation::where('ressource_id', $data['ressource_id'])
            ->whereIn('statut', $statutsActifs)
            ->where('date_fin',   '>=', now())
            ->where('date_debut', '<=', now()->addDays(30))
            ->orderBy('date_debut')
            ->get(['date_debut', 'date_fin']);

        $creneaux   = [];
        $cursor     = now()->addHour()->minute(0)->second(0);
        $limite     = now()->addDays(14);
        $tentatives = 0;
        $maxHeures  = 14 * 24;

        while (count($creneaux) < $n && $cursor < $limite && $tentatives < 300) {
            $tentatives++;
            $debut = $cursor->copy();
            $fin   = $debut->copy()->addHours($duree);

            // Règle métier : lun-ven, 8h-18h
            if (
                $debut->isWeekend()
                || $debut->hour < 8
                || $fin->gt($debut->copy()->setTime(18, 0, 0))
            ) {
                if ($debut->isWeekend() || $debut->hour >= 18) {
                    $next = $cursor->copy()->addDay()->setTime(8, 0, 0);
                    while ($next->isWeekend()) $next->addDay();
                    $cursor = $next;
                } else {
                    $cursor->addHour();
                }
                continue;
            }

            // Chercher un conflit
            $conflit = $reservations->first(function ($r) use ($debut, $fin) {
                return $debut->lt(Carbon::parse($r->date_fin))
                    && $fin->gt(Carbon::parse($r->date_debut));
            });

            if (!$conflit) {
                $heuresDepuisNow = now()->diffInHours($debut, false);
                $score = round(max(0.05, 1 - (max(0, $heuresDepuisNow) / $maxHeures)), 2);

                $creneaux[] = [
                    'debut' => $debut->format('Y-m-d H:i'),
                    'fin'   => $fin->format('Y-m-d H:i'),
                    'label' => $debut->locale('fr')->isoFormat('ddd D MMM, HH[h]mm'),
                    'score' => $score,
                ];
                $cursor = $fin->copy()->addHour();
            } else {
                $apresConflit = Carbon::parse($conflit->date_fin)->addMinutes(30);
                $minutes = $apresConflit->minute;
                $apresConflit->minute($minutes >= 30 ? 30 : 0)->second(0);
                $cursor = $apresConflit;
            }
        }

        // Journaliser
        if (count($creneaux) && auth()->user()->personnel) {
            AiSuggestion::create([
                'personnel_id'    => auth()->user()->personnel->id,
                'reservation_id'  => null,
                'type_suggestion' => 'creneau',
                'suggestion_data' => [
                    'ressource_id' => $data['ressource_id'],
                    'duree_heures' => $duree,
                    'creneaux'     => $creneaux,
                ],
                'acceptee' => false,
                'ignoree'  => false,
            ]);
        }

        return response()->json(['creneaux' => $creneaux]);
    }

    /**
     * POST /ajax/ia/ressources-similaires
     */
    public function ressourcesSimilaires(Request $request)
    {
        $data = $request->validate([
            'ressource_id' => 'required|exists:ressources,id',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after:date_debut',
        ]);

        // ✅ STATUTS CORRECTS
        $statutsActifs = ['en_attente', 'valide_chef', 'valide_admin'];

        $source = Ressource::with(['tags', 'division', 'ficheMaterielle'])
            ->findOrFail($data['ressource_id']);

        // IDs occupés sur ce créneau
        $occupes = Reservation::whereIn('statut', $statutsActifs)
            ->where('date_debut', '<', $data['date_fin'])
            ->where('date_fin',   '>', $data['date_debut'])
            ->pluck('ressource_id')
            ->toArray();

        // Candidates : même type, disponible, pas la source, pas occupée
        $candidates = Ressource::with(['tags', 'division', 'ficheMaterielle'])
            ->where('type_ressource', $source->type_ressource)
            ->where('etat', 'disponible')
            ->where('id', '!=', $source->id)
            ->whereNotIn('id', $occupes)
            ->get();

        $sourceTagIds = $source->tags->pluck('id')->toArray();

        $scored = $candidates->map(function ($r) use ($source, $sourceTagIds) {
            $rTagIds   = $r->tags->pluck('id')->toArray();
            $inter     = count(array_intersect($sourceTagIds, $rTagIds));
            $union     = count(array_unique(array_merge($sourceTagIds, $rTagIds)));
            $tagScore  = $union > 0 ? ($inter / $union) : 0;
            $divBonus  = ($r->division_id === $source->division_id) ? 0.25 : 0;
            $score     = round(min(1.0, $tagScore * 0.75 + $divBonus), 2);

            return [
                'id'          => $r->id,
                'nom'         => $r->nom,
                'division'    => $r->division?->code ?? '—',
                'localisation'=> $r->localisation ?? '—',
                'marque'      => $r->ficheMaterielle?->marque,
                'tags'        => $r->tags->pluck('libelle')->join(', '),
                'score'       => $score,
            ];
        })
        ->sortByDesc('score')
        ->take(3)
        ->values();

        // Journaliser si alternatives trouvées
        if ($scored->isNotEmpty() && auth()->user()->personnel) {
            AiSuggestion::create([
                'personnel_id'    => auth()->user()->personnel->id,
                'reservation_id'  => null,
                'type_suggestion' => 'ressource',
                'suggestion_data' => [
                    'ressource_source_id' => $source->id,
                    'date_debut'          => $data['date_debut'],
                    'date_fin'            => $data['date_fin'],
                    'ressources'          => $scored->toArray(),
                ],
                'acceptee' => false,
                'ignoree'  => false,
            ]);
        }

        return response()->json(['ressources' => $scored]);
    }
}