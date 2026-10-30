<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Vérifier les licences expirant chaque jour
        $schedule->command('geres:check-licences --days=30')
                 ->dailyAt('08:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/licences.log'));

        // Vérification urgente tous les jours
        $schedule->command('geres:check-licences --days=7')
                 ->dailyAt('08:30')
                 ->withoutOverlapping();

        // Réinitialiser le statut des ressources dont les réservations sont terminées
        $schedule->call(function () {
            \App\Models\Reservation::where('statut', 'valide_admin')
                ->where('date_fin', '<', now())
                ->each(function ($r) {
                    $r->update(['statut' => 'termine']);
                    $r->ressource->update(['etat' => 'disponible']);
                });
        })->everyFifteenMinutes()->name('liberer-ressources-terminees');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
