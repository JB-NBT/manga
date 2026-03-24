<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Retire automatiquement les mangas publics expirés (copyright, 1 an)
Schedule::command('manga:unpublish-expired')->daily();

// Refuse automatiquement les demandes de publication en attente > 30 jours
Schedule::command('publication:reject-expired')->daily();
