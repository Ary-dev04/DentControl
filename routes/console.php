<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Recordatorios de citas: cada hora
Schedule::command('citas:recordatorios')->hourly();

// Felicitaciones de cumpleaños: todos los días a las 9am
Schedule::command('pacientes:cumpleanos')->dailyAt('09:00');
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
