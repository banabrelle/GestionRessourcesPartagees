<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    DashboardController,
    RessourceController,
    ReservationController,
    PersonnelController,
    DivisionController,
    ServiceController,
    BureauController,
    LicenceController,
    RessourceLogicielleController,
    NotificationController,
    StatistiqueController,
    ExportController,
    BonEmpruntController,
    TagController,
    AiSuggestionController,
    PasswordChangeController,  // ← AJOUT
};

// ──────────────────────────────────────────────────────────────────────────────
// ACCUEIL → Login
// ──────────────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ──────────────────────────────────────────────────────────────────────────────
// AUTHENTIFICATION (généré par Breeze)
// ──────────────────────────────────────────────────────────────────────────────
require __DIR__.'/auth.php';

// ──────────────────────────────────────────────────────────────────────────────
// CHANGEMENT MOT DE PASSE OBLIGATOIRE (1ère connexion)         // ← AJOUT
// Séparé du groupe principal : pas de force.password ici        // ← AJOUT
// ──────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {                 // ← AJOUT
    Route::get('/changer-mot-de-passe',                         // ← AJOUT
        [PasswordChangeController::class, 'show'])              // ← AJOUT
        ->name('changer-mot-de-passe.show');                              // ← AJOUT
    Route::post('/changer-mot-de-passe',                        // ← AJOUT
        [PasswordChangeController::class, 'update'])            // ← AJOUT
        ->name('changer-mot-de-passe.update');                              // ← AJOUT
});                                                             // ← AJOUT

// ──────────────────────────────────────────────────────────────────────────────
// ROUTES PROTÉGÉES — Un seul groupe auth englobant TOUT
// ──────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'force.password'])->group(function () { // ← AJOUT force.password

    // ══════════════════════════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════════════════════════
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');

    // ══════════════════════════════════════════════════════════════════════════
    // PROFIL (Breeze)
    // ══════════════════════════════════════════════════════════════════════════
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ══════════════════════════════════════════════════════════════════════════
    // RESSOURCES
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('ressources')->name('ressources.')->group(function () {
        Route::get('/',            [RessourceController::class, 'index'])->name('index');
        Route::get('/{ressource}', [RessourceController::class, 'show'])->name('show');

        Route::middleware('can:gerer ressources')->group(function () {
            Route::post('/',                  [RessourceController::class, 'store'])->name('store');
            Route::put('/{ressource}',        [RessourceController::class, 'update'])->name('update');
            Route::delete('/{ressource}',     [RessourceController::class, 'destroy'])->name('destroy');
            Route::patch('/{ressource}/etat', [RessourceController::class, 'updateEtat'])->name('update-etat');
        });
    });

    // ══════════════════════════════════════════════════════════════════════════
    // RÉSERVATIONS
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/',              [ReservationController::class, 'index'])->name('index');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');

        Route::post('/', [ReservationController::class, 'store'])
             ->middleware('can:creer reservations')
             ->name('store');

        Route::patch('/{reservation}/annuler',
            [ReservationController::class, 'annuler'])->name('annuler');

        Route::patch('/{reservation}/valider-chef',
            [ReservationController::class, 'validerChef'])->name('valider-chef');

        Route::patch('/{reservation}/rejeter-chef',
            [ReservationController::class, 'rejeterChef'])->name('rejeter-chef');

        Route::patch('/{reservation}/valider-admin',
            [ReservationController::class, 'validerAdmin'])
             ->middleware('can:valider admin reservations')
             ->name('valider-admin');

        Route::patch('/{reservation}/rejeter-admin',
            [ReservationController::class, 'rejeterAdmin'])
             ->middleware('can:valider admin reservations')
             ->name('rejeter-admin');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // BONS D'EMPRUNT
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('bons')->name('bons.')->group(function () {
        Route::get('/',                  [BonEmpruntController::class, 'index'])->name('index');
        Route::get('/{bon}/telecharger', [BonEmpruntController::class, 'telecharger'])->name('telecharger');
        Route::get('/{bon}/apercu',      [BonEmpruntController::class, 'apercu'])->name('apercu');
        Route::patch('/{bon}/retour',    [BonEmpruntController::class, 'enregistrerRetour'])
             ->middleware('can:gerer ressources')
             ->name('retour');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // PERSONNEL / UTILISATEURS
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('personnels')->name('personnels.')->middleware('can:voir personnel')->group(function () {
        Route::get('/',            [PersonnelController::class, 'index'])->name('index');
        Route::get('/{personnel}', [PersonnelController::class, 'show'])->name('show');

        Route::middleware('can:creer personnel')->group(function () {
            Route::post('/', [PersonnelController::class, 'store'])->name('store');
        });
        Route::middleware('can:modifier personnel')->group(function () {
            Route::put('/{personnel}',          [PersonnelController::class, 'update'])->name('update');
            Route::patch('/{personnel}/toggle', [PersonnelController::class, 'toggleStatut'])->name('toggle');

            // Réinitialiser le MDP d'un agent (admin remet must_change_password = true) // ← AJOUT
            Route::patch('/{personnel}/reinitialiser-mdp',                               // ← AJOUT
                [PersonnelController::class, 'reinitialiserMotDePasse'])                 // ← AJOUT
                ->name('reinitialiser-mdp');                                             // ← AJOUT
        });
        Route::middleware('can:supprimer personnel')->group(function () {
            Route::delete('/{personnel}', [PersonnelController::class, 'destroy'])->name('destroy');
        });
    });

    // ══════════════════════════════════════════════════════════════════════════
    // PARAMÈTRES — Page principale (onglets)
    // ══════════════════════════════════════════════════════════════════════════
    Route::get('/parametres', function () {
        $activeTab = request('tab', 'divisions');

        $divisions = \App\Models\Division::with('chef')
            ->withCount(['personnels', 'ressources', 'services'])
            ->orderBy('code')
            ->paginate(10)
            ->appends(['tab' => 'divisions']);

        $services = \App\Models\Service::with('division')
            ->withCount(['personnels', 'bureaux'])
            ->orderBy('nom')
            ->paginate(10)
            ->appends(['tab' => 'services']);

        $bureaux = \App\Models\Bureau::with(['service.division'])
            ->withCount('personnels')
            ->orderBy('nom')
            ->paginate(10)
            ->appends(['tab' => 'bureaux']);

        $personnelsQuery = \App\Models\Personnel::with(['user', 'division', 'service', 'bureau'])
            ->orderBy('nom');

        if (request('search')) {
            $personnelsQuery->where(function ($q) {
                $q->where('nom',        'like', '%' . request('search') . '%')
                  ->orWhere('prenom',   'like', '%' . request('search') . '%')
                  ->orWhere('matricule','like', '%' . request('search') . '%');
            });
        }
        if (request('division_id')) {
            $personnelsQuery->where('division_id', request('division_id'));
        }

        $personnels = $personnelsQuery->paginate(10)
            ->appends(array_merge(request()->except('page'), ['tab' => 'utilisateurs']));

        $toutsDivisions = \App\Models\Division::orderBy('code')->get();
        $tousServices   = \App\Models\Service::with('division')->orderBy('nom')->get();
        $tousBureaux    = \App\Models\Bureau::orderBy('nom')->get();
        $tousChefs      = \App\Models\Personnel::with('user')->get();

        return view('parametres.index', compact(
            'divisions', 'services', 'bureaux', 'personnels',
            'toutsDivisions', 'tousServices', 'tousBureaux', 'tousChefs', 'activeTab'
        ));
    })->name('parametres.index');

    // Redirections rapides onglets
    Route::get('/divisions', fn() => redirect()->route('parametres.index', ['tab' => 'divisions']))->name('divisions.index');
    Route::get('/services',  fn() => redirect()->route('parametres.index', ['tab' => 'services']))->name('services.index');
    Route::get('/bureaux',   fn() => redirect()->route('parametres.index', ['tab' => 'bureaux']))->name('bureaux.index');

    // ──────────────────────────────────────────────────────────────────────────
    // CRUD DIVISIONS (AJAX JSON — modales)                       // ← AJOUT
    // ──────────────────────────────────────────────────────────────────────────
    Route::prefix('divisions')->name('divisions.')->middleware('can:gerer parametres')->group(function () { // ← AJOUT
        Route::post('/',                          [DivisionController::class, 'store'])->name('store');    // ← AJOUT
        Route::put('/{division}',                 [DivisionController::class, 'update'])->name('update'); // ← AJOUT
        Route::delete('/{division}',              [DivisionController::class, 'destroy'])->name('destroy');// ← AJOUT
        Route::patch('/{division}/toggle-actif',  [DivisionController::class, 'toggleActif'])->name('toggle-actif'); // ← AJOUT
    });                                                                                                    // ← AJOUT

    // ──────────────────────────────────────────────────────────────────────────
    // CRUD SERVICES (AJAX JSON — modales)                        // ← AJOUT
    // ──────────────────────────────────────────────────────────────────────────
    Route::prefix('services')->name('services.')->middleware('can:gerer parametres')->group(function () {  // ← AJOUT
        Route::post('/',                                    [ServiceController::class, 'store'])->name('store');    // ← AJOUT
        Route::put('/{service}',                            [ServiceController::class, 'update'])->name('update'); // ← AJOUT
        Route::delete('/{service}',                         [ServiceController::class, 'destroy'])->name('destroy');// ← AJOUT
        Route::get('/par-division/{division}',              [ServiceController::class, 'parDivision'])->name('par-division'); // ← AJOUT
    });                                                                                                    // ← AJOUT

    // ──────────────────────────────────────────────────────────────────────────
    // CRUD BUREAUX (AJAX JSON — modales)                         // ← AJOUT
    // ──────────────────────────────────────────────────────────────────────────
    Route::prefix('bureaux')->name('bureaux.')->middleware('can:gerer parametres')->group(function () {    // ← AJOUT
        Route::post('/',                          [BureauController::class, 'store'])->name('store');     // ← AJOUT
        Route::put('/{bureau}',                   [BureauController::class, 'update'])->name('update');  // ← AJOUT
        Route::delete('/{bureau}',                [BureauController::class, 'destroy'])->name('destroy');// ← AJOUT
        Route::get('/par-service/{service}',      [BureauController::class, 'parService'])->name('par-service'); // ← AJOUT
    });                                                                                                    // ← AJOUT

    // ══════════════════════════════════════════════════════════════════════════
    // LICENCES LOGICIELLES
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('licences')->name('licences.')->middleware('can:gerer licences')->group(function () {
        Route::get('/',                    [RessourceLogicielleController::class, 'index'])->name('index');
        Route::post('/',                   [RessourceLogicielleController::class, 'store'])->name('store');
        Route::put('/{licence}',           [RessourceLogicielleController::class, 'update'])->name('update');
        Route::delete('/{licence}',        [RessourceLogicielleController::class, 'destroy'])->name('destroy');
        Route::patch('/{licence}/allouer', [RessourceLogicielleController::class, 'allouer'])->name('allouer');
        Route::patch('/{licence}/liberer', [RessourceLogicielleController::class, 'liberer'])->name('liberer');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // TAGS
    // ══════════════════════════════════════════════════════════════════════════
    Route::resource('tags', TagController::class)
         ->middleware('can:gerer tags')
         ->only(['index', 'store', 'update', 'destroy']);

    // ══════════════════════════════════════════════════════════════════════════
    // STATISTIQUES
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('statistiques')->name('statistiques.')->middleware('can:voir statistiques')->group(function () {
        Route::get('/',             [StatistiqueController::class, 'index'])->name('index');
        Route::get('/ressources',   [StatistiqueController::class, 'ressources'])->name('ressources');
        Route::get('/divisions',    [StatistiqueController::class, 'divisions'])->name('divisions');
        Route::get('/reservations', [StatistiqueController::class, 'reservations'])->name('reservations');
        Route::get('/temporelles',  [StatistiqueController::class, 'temporelles'])->name('temporelles');
        Route::get('/utilisateurs', [StatistiqueController::class, 'utilisateurs'])->name('utilisateurs');
        Route::get('/licences',     [StatistiqueController::class, 'licences'])->name('licences');
        Route::get('/ia',           [StatistiqueController::class, 'ia'])->name('ia');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // EXPORTS
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('export')->name('export.')->middleware('can:exporter rapports')->group(function () {
        Route::get('/reservations-pdf',   [ExportController::class, 'reservationsPdf'])->name('reservations-pdf');
        Route::get('/reservations-excel', [ExportController::class, 'reservationsExcel'])->name('reservations-excel');
        Route::get('/inventaire-pdf',     [ExportController::class, 'inventairePdf'])->name('inventaire-pdf');
        Route::get('/statistiques-pdf',   [ExportController::class, 'statistiquesPdf'])->name('statistiques-pdf');
        Route::get('/statistiques-division/{division}',
            [ExportController::class, 'statistiquesDivisionPdf'])
            ->name('statistiques-division-pdf');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // IA — Vues journal + AJAX
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/',             [AiSuggestionController::class, 'index'])->name('index');
        Route::get('/statistiques', [AiSuggestionController::class, 'statistiques'])->name('statistiques');
        Route::get('/{suggestion}', [AiSuggestionController::class, 'show'])->name('show');
        Route::patch('/{suggestion}/accepter', [AiSuggestionController::class, 'accepter'])->name('accepter');
        Route::patch('/{suggestion}/ignorer',  [AiSuggestionController::class, 'ignorer'])->name('ignorer');
    });

    Route::prefix('ajax/ia')->name('ajax.ia.')->group(function () {
        Route::post('/verifier-conflit',      [AiSuggestionController::class, 'verifierConflit'])->name('verifier-conflit');
        Route::post('/suggerer-creneaux',     [AiSuggestionController::class, 'suggererCreneaux'])->name('suggerer-creneaux');
        Route::post('/ressources-similaires', [AiSuggestionController::class, 'ressourcesSimilaires'])->name('ressources-similaires');
    });

    // ══════════════════════════════════════════════════════════════════════════
    // NOTIFICATIONS
    // ══════════════════════════════════════════════════════════════════════════
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                      [NotificationController::class, 'index'])->name('index');
        Route::patch('/{notification}/lire', [NotificationController::class, 'marquerLue'])->name('lire');
        Route::patch('/lire-tout',           [NotificationController::class, 'marquerToutLu'])->name('lire-tout');
    });

}); // fin middleware auth