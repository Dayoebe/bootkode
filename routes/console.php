<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Laravel 12 loads this file through bootstrap/app.php; this is the single schedule source.
Schedule::command('newsletter:send-scheduled')
    ->everyMinute()
    ->withoutOverlapping()
    ->when(fn (): bool => config('delivery.newsletter_schedule_enabled'));

Schedule::command('gamification:reset-daily-quests')
    ->dailyAt('00:01')
    ->withoutOverlapping();

Schedule::command('reviews:generate-analytics')
    ->dailyAt('01:00')
    ->withoutOverlapping();

// Money-moving automation remains visible to schedule:list but is opt-in.
Schedule::command('withdrawals:process')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->when(fn (): bool => config('delivery.withdrawal_processing_schedule_enabled'));

Schedule::command('bootkode:job-alerts')
    ->hourly()
    ->withoutOverlapping()
    ->when(fn (): bool => config('delivery.job_alerts_schedule_enabled'));

Schedule::command('bootkode:cbt-reminders')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->when(fn (): bool => config('delivery.learning_reminders_schedule_enabled'));

Schedule::command('reviews:send-reminders')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->when(fn (): bool => config('delivery.learning_reminders_schedule_enabled'));
