<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Schedule as BusSchedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Command to update finished schedule statuses
Artisan::command('schedules:update-status', function () {
    $this->info('Updating finished schedule statuses...');

    // Update all scheduled schedules that have finished to 'departed' status
    $updatedCount = BusSchedule::where('status', 'scheduled')
        ->whereRaw("CONCAT(travel_date, ' ', departure_time) <= NOW()")
        ->update(['status' => 'departed']);

    $this->info("Updated {$updatedCount} schedules to 'departed' status.");

    return 0;
})->purpose('Update finished schedule statuses to departed');

// Schedule the command to run every minute
Schedule::command('schedules:update-status')->everyMinute();

// Schedule seat reservation cleanup every 5 minutes
Schedule::command('reservations:cleanup --send-notifications')->everyFiveMinutes();
