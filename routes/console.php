<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('whatsapp:plan-patient-reminders')
    ->everyFiveMinutes()
    ->withoutOverlapping(10);

Schedule::command('whatsapp:dispatch-patient-tasks')
    ->everyMinute()
    ->withoutOverlapping(5);
