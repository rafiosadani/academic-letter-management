<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// SCHEDULED TASKS
// ============================================================

/**
 * Check Semester Schedule
 *
 * Runs daily at midnight (00:00 WIB)
 * Checks if any semester should be activated based on current date
 */
//Schedule::command('app:check-semester-schedule')
//    ->dailyAt('00:00')
//    ->timezone('Asia/Jakarta')
//    ->onSuccess(function () {
//        logger()->info('✅ CheckSemesterSchedule completed successfully');
//    })
//    ->onFailure(function () {
//        logger()->error('❌ CheckSemesterSchedule failed');
//    });

if (config('app.env') === 'production') {
    Schedule::command('app:check-semester-schedule')
        ->dailyAt('00:00')
        ->timezone('Asia/Jakarta')
        ->onSuccess(function () {
            logger()->info('✅ CheckSemesterSchedule completed successfully');
        })
        ->onFailure(function () {
            logger()->error('❌ CheckSemesterSchedule failed');
        });;
}

// Atau pakai everyMinute() untuk testing
if (config('app.env') === 'local') {
    Schedule::command('app:check-semester-schedule')
        ->everyMinute() // Testing: jalan tiap menit
        ->timezone('Asia/Jakarta')
        ->onSuccess(function () {
            logger()->info('✅ CheckSemesterSchedule completed successfully');
        })
        ->onFailure(function () {
            logger()->error('❌ CheckSemesterSchedule failed');
        });;
}

/**
 * Send Daily Notification Digest
 *
 * Runs daily at 08:00 WIB
 * Sends email digest to users who have enabled daily digest
 */
Schedule::command('app:send-daily-notification-digest')
    ->dailyAt('08:00')
    ->timezone('Asia/Jakarta')
    ->onSuccess(function () {
        logger()->info('✅ SendDailyNotificationDigest completed successfully');
    })
    ->onFailure(function () {
        logger()->error('❌ SendDailyNotificationDigest failed');
    });
