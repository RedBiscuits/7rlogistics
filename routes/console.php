<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule pickup reminder notifications to run daily
Schedule::command('shipments:send-pickup-reminders')
    ->daily()
    ->at('09:00')
    ->description('Send pickup reminder notifications to drivers 1 day before pickup date');
