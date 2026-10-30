<?php
namespace App\Services;

use App\Models\{Ressource, Personnel, AiSuggestion, Tag};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class AiSuggestionService
{
    public function __construct(
        private ConflictDetectionService $conflictSvc
    ) {}

    /**
     * Point d'entrée principal du module IA.
     * Génère les suggestions quand un conflit est détecté.
     *
     * @return array ['creneaux' => [...], 'ressources' => [...]]
     */
    public function suggerer(
        Ressource $ressource,
        string    $dateDebut,
        string    $dateFin,
        Personnel $personnel
    ): array {
        $dureeH      = (int) round((strtotime($dateFin) - strtotime($dateDebut)) / 3600);
        $suggestions = ['creneaux' => [], 'ressources' => []];

        // ── Moteur temporel: 3 prochains créneaux libres ────────
        $suggestions['creneaux'] = $this->conflictSvc->prochainCreneaux(
            $ressource, max(1, $dureeH), 3
        );

        // ── Moteur de substitution: ressources similaires ────────
        $suggestions['ressources'] = $this->trouverRessourcesSimilaires(
            $ressource, $dateDebut, $dateFin
        );

        // ── Journaliser en base pour les statistiques IA ─────────
        $aiSuggestion = AiSuggestion::create([
            'personnel_id'    => $personnel->id,
            'type_suggestion' => 'creneau',
            'suggestion_data' => $suggestions,
        ]);

        // Ajouter l'ID de suggestion pour le feedback (accepter/ignorer)
        $suggestions['suggestion_id'] = $aiSuggestion->id;

        return $suggestions;
    }

    /**
     * Moteur de substitution par similarité de tags.
     * Trouve les ressources du même type avec des tags similaires
     * et qui sont disponibles sur la période souhaitée.
     */
    private function trouverRessourcesSimilaires(
        Ressource $ressource,
        string    $dateDebut,
        string    $dateFin
    ): array {
        $tagsIds = $ressource->tags->pluck('id');

        // Trouver des ressources similaires par type + tags
        $candidates = Ressource::where('id', '!=', $ressource->id)
            ->where('type_ressource', $ressource->type_ressource)
            ->where('etat', Ressource::ETAT_DISPONIBLE)
            ->with(['tags', 'division', 'ficheMaterielle'])
            ->get();

        // Calculer score de similarité pour chaque ressource
        $scored = $candidates->map(function (Ressource $r) use ($tagsIds, $dateDebut, $dateFin) {
            // Vérifier disponibilité réelle sur la période
            if (!$this->conflictSvc->prochainCreneaux($r, 1, 1)) return null;
            if ($this->conflictSvc->detecter($r, $dateDebut, $dateFin)) return null;

            // Score = intersection des tags / union des tags (Jaccard)
            $rTagsIds    = $r->tags->pluck('id');
            $intersection = $tagsIds->intersect($rTagsIds)->count();
            $union        = $tagsIds->union($rTagsIds)->count();
            $score        = $union > 0 ? round($intersection / $union, 2) : 0;

            return [
                'id'          => $r->id,
                'nom'         => $r->nom,
                'localisation'=> $r->localisation ?? '—',
                'division'    => $r->division?->code ?? '—',
                'marque'      => $r->ficheMaterielle?->marque ?? null,
                'score'       => $score,
                'tags'        => $r->tags->pluck('libelle')->implode(', '),
            ];
        })
        ->filter() // supprimer les nulls
        ->sortByDesc('score')
        ->take(3)
        ->values()
        ->toArray();

        return $scored;
    }

    /**
     * Prédire la ressource la plus adaptée pour un motif donné.
     * Analyse les mots-clés du motif pour trouver les tags correspondants.
     */
    public function predireRessourcePourMotif(string $motif): Collection
    {
        // Extraire les mots-clés du motif (simple NLP PHP)
        $motsMots = collect(explode(' ', strtolower($motif)))
            ->filter(fn($m) => strlen($m) > 3)
            ->unique();

        // Trouver les tags correspondants
        $tagsCorrespondants = Tag::where(function ($q) use ($motsMots) {
            foreach ($motsMots as $mot) {
                $q->orWhere('libelle', 'like', "%{$mot}%");
            }
        })->pluck('id');

        if ($tagsCorrespondants->isEmpty()) {
            return collect();
        }

        // Retourner les ressources avec ces tags, disponibles
        return Ressource::whereHas('tags',
                fn($q) => $q->whereIn('tags.id', $tagsCorrespondants)
            )->where('etat', Ressource::ETAT_DISPONIBLE)
            ->with(['tags','division'])->take(5)->get();
    }

    /**
     * Statistiques du module IA pour le tableau de bord.
     */
    public function statistiques(): array
    {
        $total     = AiSuggestion::count();
        $acceptees = AiSuggestion::where('acceptee', true)->count();
        $ignorees  = AiSuggestion::where('ignoree',  true)->count();

        return [
            'total'              => $total,
            'acceptees'          => $acceptees,
            'ignorees'           => $ignorees,
            'en_attente'         => $total - $acceptees - $ignorees,
            'taux_acceptation'   => $total > 0 ? round($acceptees / $total * 100) : 0,
            'temps_total_gagne'  => AiSuggestion::sum('temps_gagne_minutes'),
        ];
    }
}

