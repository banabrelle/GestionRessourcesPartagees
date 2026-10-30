<?php
namespace App\Console\Commands;

use App\Models\{RessourcesLogicielle, User};
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckLicenseExpiration extends Command
{
    protected $signature   = 'geres:check-licences {--days=30 : Alerter X jours avant expiration}';
    protected $description = 'Vérifier les licences logicielles arrivant à expiration et envoyer des alertes.';

    public function __construct(private NotificationService $notifSvc)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $jours = (int) $this->option('days');
        $this->info("Vérification des licences expirant dans {$jours} jours...");

        $licences = RessourcesLogicielle::with('ressource')
            ->whereNotNull('date_fin_licence')
            ->where('date_fin_licence', '>=', now())
            ->where('date_fin_licence', '<=', now()->addDays($jours))
            ->where('alerte_envoyee', false)
            ->get();

        if ($licences->isEmpty()) {
            $this->info('Aucune licence à alerter.');
            return Command::SUCCESS;
        }

        // Récupérer tous les administrateurs
        $admins = User::role('administrateur')->get();

        foreach ($licences as $licence) {
            $joursRestants = now()->diffInDays($licence->date_fin_licence);
            $nomRessource  = $licence->ressource->nom;

            $this->line("  → Alerte: {$nomRessource} expire dans {$joursRestants} jour(s)");

            // Notifier tous les admins
            foreach ($admins as $admin) {
                $this->notifSvc->envoyer(
                    $admin,
                    'licence_expiration',
                    "Licence arrivant à expiration",
                    "La licence '{$nomRessource}' ({$licence->editeur}) expire dans {$joursRestants} jour(s). Pensez à la renouveler.",
                    [
                        'ressource_id'    => $licence->ressource_id,
                        'jours_restants'  => $joursRestants,
                        'date_expiration' => $licence->date_fin_licence->format('d/m/Y'),
                    ]
                );
            }

            // Marquer l'alerte comme envoyée
            $licence->update(['alerte_envoyee' => true]);
        }

        $this->info("✅ {$licences->count()} alerte(s) envoyée(s).");
        return Command::SUCCESS;
    }
}
