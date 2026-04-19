<?php

use Illuminate\Support\Facades\Schedule;

// Auto-mark absent setiap hari kerja pukul 17:05 WIB
Schedule::command('attendance:mark-absent')
    ->weekdays()
    ->timezone('Asia/Jakarta')
    ->at('17:05')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
