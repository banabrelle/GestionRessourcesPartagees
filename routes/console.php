<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckLicenseExpiration;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Alternative Laravel 12 (sans Kernel.php)
Schedule::command('geres:check-licences --days=30')->dailyAt('08:00');
Schedule::command('geres:check-licences --days=7')->dailyAt('08:30');

// Exécution manuelle:
// php artisan geres:check-licences
// php artisan geres:check-licences --days=7

// Lancer le scheduler en production (crontab):
// * * * * * cd /var/www/GERES_CENADI && php artisan schedule:run >> /dev/null 2>&1

