<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jalankan setiap tanggal 1 jam 08:00 pagi
Schedule::command('invoice:generate-monthly')->monthlyOn(1, '08:00');
