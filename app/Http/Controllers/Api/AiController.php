<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Ressource, AiSuggestion};
use App\Services\{ConflictDetectionService, AiSuggestionService, AiScoringService};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AiController extends Controller
{
    public function __construct(
        private ConflictDetectionService $conflictSvc,
        private AiSuggestionService      $aiSvc,
        private AiScoringService         $scoringSvc
    ) {}

    /**
     * POST /api/ia/verifier-conflit
     * Utilisé par le formulaire de réservation pour vérifier en temps réel.
     */
    public function verifierConflit(Request $request): JsonResponse
    {
        $v = $request->validate([
            'ressource_id' => 'required|exists:ressources,id',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after:date_debut',
        ]);

        $ressource = Ressource::findOrFail($v['ressource_id']);
        $conflit   = $this->conflictSvc->detecter($ressource, $v['date_debut'], $v['date_fin']);

        return response()->json([
            'conflit'  => $conflit,
            'ressource'=> $ressource->nom,
            'etat'     => $ressource->etat,
        ]);
    }

    /**
     * POST /api/ia/suggerer-creneaux
     * Retourne N créneaux disponibles pour une ressource.
     */
    public function suggererCreneaux(Request $request): JsonResponse
    {
        $v = $request->validate([
            'ressource_id' => 'required|exists:ressources,id',
            'duree_heures' => 'required|integer|min:1|max:12',
            'n'            => 'nullable|integer|min:1|max:5',
        ]);

        $ressource = Ressource::findOrFail($v['ressource_id']);
        $creneaux  = $this->conflictSvc->prochainCreneaux(
            $ressource,
            $v['duree_heures'],
            $v['n'] ?? 3
        );

        return response()->json(['creneaux' => $creneaux]);
    }

    /**
     * POST /api/ia/ressources-similaires
     * Trouve des ressources similaires disponibles.
     */
    public function ressourcesSimilaires(Request $request): JsonResponse
    {
        $v = $request->validate([
            'ressource_id' => 'required|exists:ressources,id',
            'date_debut'   => 'required|date',
            'date_fin'     => 'required|date|after:date_debut',
        ]);

        $ressource  = Ressource::with('tags')->findOrFail($v['ressource_id']);
        $personnel  = auth()->user()->personnel;
        $suggestions = $this->aiSvc->suggerer(
            $ressource, $v['date_debut'], $v['date_fin'], $personnel
        );

        return response()->json($suggestions);
    }

    /**
     * PATCH /api/ia/suggestions/{suggestion}/accepter
     * Enregistre l'acceptation d'une suggestion pour les stats.
     */
    public function accepterSuggestion(AiSuggestion $suggestion): JsonResponse
    {
        abort_unless($suggestion->personnel_id === auth()->user()->personnel?->id, 403);

        $suggestion->update([
            'acceptee'            => true,
            'ignoree'             => false,
            'temps_gagne_minutes' => 15, // Estimation temps gagné
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Suggestion IA acceptée. Merci pour le retour !',
        ]);
    }

    /**
     * PATCH /api/ia/suggestions/{suggestion}/ignorer
     * Enregistre l ignorance d une suggestion.
     */
    public function ignorerSuggestion(AiSuggestion $suggestion): JsonResponse
    {
        abort_unless($suggestion->personnel_id === auth()->user()->personnel?->id, 403);

        $suggestion->update(['ignoree' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Suggestion ignorée.',
        ]);
    }

    /**
     * GET /api/ia/statistiques
     * Statistiques du module IA pour le tableau de bord.
     */
    public function statistiques(): JsonResponse
    {
        $stats = $this->aiSvc->statistiques();
        $conflits = $this->conflictSvc->statistiquesConflits();

        return response()->json([
            ...$stats,
            ...$conflits,
        ]);
    }
}

