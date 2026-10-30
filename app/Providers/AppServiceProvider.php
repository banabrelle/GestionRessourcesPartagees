<?php
namespace App\Providers;

use App\Services\{
    ConflictDetectionService,
    AiSuggestionService,
    AiScoringService,
    NotificationService,
    BonEmpruntService,
};
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Enregistrer les services comme singletons
        $this->app->singleton(ConflictDetectionService::class);
        $this->app->singleton(AiSuggestionService::class);
        $this->app->singleton(AiScoringService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(BonEmpruntService::class);
    }

    public function boot(): void
    {
        // Partager les notifications non lues à toutes les vues Blade
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $view->with('_nbNotifs',
                    auth()->user()->notificationsNonLues()->count()
                );
            }
        });
        Paginator::defaultView('vendor.pagination.bootstrap-5');
    }
}
