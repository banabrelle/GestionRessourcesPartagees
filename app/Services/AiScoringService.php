<?php
namespace App\Services;

use App\Models\{Ressource, Personnel};
use Illuminate\Support\Facades\DB;

/**
 * Calcule un score de pertinence pour classer les suggestions IA.
 * Prend en compte l'historique d'utilisation, la division, les tags.
 */
class AiScoringService
{
    /**
     * Score global d'une ressource pour un personnel donné.
     * Score entre 0 (mauvais) et 1 (parfait).
     */
    public function scorer(Ressource $ressource, Personnel $personnel): float
    {
        $score = 0.0;

        // Facteur 1: Ressource de la même division (0.4 pts)
        if ($ressource->division_id === $personnel->division_id) {
            $score += 0.4;
        }

        // Facteur 2: Ressource déjà utilisée par ce personnel (0.3 pts)
        $dejaUtilisee = $ressource->reservations()
            ->where('personnel_id', $personnel->id)
            ->where('statut', 'valide_admin')
            ->exists();
        if ($dejaUtilisee) $score += 0.3;

        // Facteur 3: Taux de disponibilité historique (0.2 pts max)
        $totalRes     = $ressource->reservations()->count();
        $tauxDispo    = $totalRes > 0 ? max(0, 1 - ($totalRes / 100)) : 1.0;
        $score       += $tauxDispo * 0.2;

        // Facteur 4: Ressource récemment remise en service (0.1 pts)
        $recenteMaintenance = $ressource->reservations()
            ->where('statut', 'termine')
            ->where('updated_at', '>=', now()->subDays(7))
            ->exists();
        if (!$recenteMaintenance) $score += 0.1;

        return round(min(1.0, $score), 2);
    }

    /**
     * Classer une liste de ressources par score décroissant.
     */
    public function classerRessources(
        iterable  $ressources,
        Personnel $personnel
    ): array {
        return collect($ressources)
            ->map(fn($r) => [
                'ressource' => $r,
                'score'     => $this->scorer($r, $personnel),
            ])
            ->sortByDesc('score')
            ->values()
            ->toArray();
    }
}