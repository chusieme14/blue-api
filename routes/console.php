<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::command('app:get-bouy-data')
    ->daily()
    ->runInBackground();

Schedule::command('app:backup-file')
    ->dailyAt('03:00')
    ->runInBackground();