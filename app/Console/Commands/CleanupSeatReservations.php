<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SeatReservationService;

class CleanupSeatReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:cleanup 
                            {--send-notifications : Send expiry notifications before cleanup}
                            {--dry-run : Show what would be cleaned up without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired seat reservations and send expiry notifications';

    /**
     * The seat reservation service.
     */
    protected $reservationService;

    /**
     * Create a new command instance.
     */
    public function __construct(SeatReservationService $reservationService)
    {
        parent::__construct();
        $this->reservationService = $reservationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting seat reservation cleanup...');

        $sendNotifications = $this->option('send-notifications');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No actual changes will be made');
        }

        // Send expiry notifications first if requested
        if ($sendNotifications) {
            $this->info('Sending expiry notifications...');
            
            if (!$dryRun) {
                $notificationCount = $this->reservationService->sendExpiryNotifications();
                $this->info("Sent {$notificationCount} expiry notifications.");
            } else {
                $this->info('Would send expiry notifications (dry run)');
            }
        }

        // Clean up expired reservations
        $this->info('Cleaning up expired reservations...');
        
        if (!$dryRun) {
            $cleanedCount = $this->reservationService->cleanupExpiredReservations();
            $this->info("Cleaned up {$cleanedCount} expired reservations.");
        } else {
            $this->info('Would clean up expired reservations (dry run)');
        }

        $this->info('Seat reservation cleanup completed.');

        return 0;
    }
}
