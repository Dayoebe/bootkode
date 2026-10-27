<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The operator still needs the normal Laravel scheduler; verification uses dry-run/test databases.
if (config('delivery.job_alerts_schedule_enabled')) {
    \Illuminate\Support\Facades\Schedule::command('bootkode:job-alerts')->hourly()->withoutOverlapping();
}
