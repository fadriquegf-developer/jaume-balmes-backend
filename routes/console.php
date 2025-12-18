<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//Enviar recordatoris
Schedule::command('open-doors:send-reminders')->dailyAt('09:00');

//Enviar formularis post-visita
Schedule::command('surveys:send-post-visit')->dailyAt('10:00');
