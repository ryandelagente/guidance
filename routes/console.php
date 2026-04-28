<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Daily 8 AM: email reminders for tomorrow's confirmed appointments ──
Schedule::command('app:send-appointment-reminders')
    ->dailyAt('08:00')
    ->timezone('Asia/Manila')
    ->withoutOverlapping()
    ->onOneServer();
