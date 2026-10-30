<?php
use App\Http\Controllers\Api\{
    AiController,
    RessourceApiController,
    ReservationApiController,
};
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // ══════════════════════════════════════════════════════════
    // MODULE IA — Détection conflits & suggestions
    // ══════════════════════════════════════════════════════════
    Route::prefix('ia')->name('api.ia.')->group(function () {

        // Vérifier conflit en temps réel (appelé par JS dans le formulaire)
        Route::post('/verifier-conflit',   [AiController::class, 'verifierConflit'])
             ->name('conflit');

        // Obtenir des créneaux disponibles
        Route::post('/suggerer-creneaux',  [AiController::class, 'suggererCreneaux'])
             ->name('creneaux');

        // Obtenir des ressources similaires disponibles
        Route::post('/ressources-similaires', [AiController::class, 'ressourcesSimilaires'])
             ->name('similaires');

        // Feedback utilisateur sur les suggestions
        Route::patch('/suggestions/{suggestion}/accepter', [AiController::class, 'accepterSuggestion'])
             ->name('accepter');
        Route::patch('/suggestions/{suggestion}/ignorer',  [AiController::class, 'ignorerSuggestion'])
             ->name('ignorer');

        // Statistiques IA
        Route::get('/statistiques', [AiController::class, 'statistiques'])
             ->name('stats');
    });

    // ══════════════════════════════════════════════════════════
    // DONNÉES POUR VUE.JS
    // ══════════════════════════════════════════════════════════

    // ResourceMonitor.vue — états en temps réel
    Route::get('/ressources/etats', [RessourceApiController::class, 'etats'])
         ->name('api.ressources.etats');

    // StatCharts.vue — données graphiques
    Route::get('/statistiques/graphiques', [RessourceApiController::class, 'statsGraphiques'])
         ->name('api.stats.graphiques');

    // CalendarView.vue — réservations calendrier
    Route::get('/reservations/calendrier', [ReservationApiController::class, 'calendrier'])
         ->name('api.reservations.calendrier');

    // Badge notifications navbar
    Route::get('/notifications/count', function () {
        return response()->json([
            'count' => auth()->user()->notificationsNonLues()->count(),
        ]);
    })->name('api.notifications.count');
});
