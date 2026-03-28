<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler pour nettoyer les sessions expirées
Schedule::command('sessions:cleanup')->everyFiveMinutes();

// Filet de sécurité quotidien : Vérification des niveaux de stock
Schedule::command('stock:check')->daily();
