<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateSystemNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate-system {--test : Generate test notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate system notifications based on current data and events';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('test')) {
            $this->generateTestNotifications();
            return;
        }

        $this->info('Generating system notifications...');

        $this->checkPendingBookings();
        $this->checkHighVolumeBookings();
        $this->checkCancelledBookings();
        $this->checkInactiveOperators();
        $this->checkRevenueTargets();
        $this->checkSystemHealth();

        $this->info('System notifications generated successfully!');
    }

    private function checkPendingBookings()
    {
        $pendingCount = Booking::where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subHours(2))
            ->count();

        if ($pendingCount > 10) {
            $this->notificationService->sendAdminNotification(
                'booking_alert',
                'High Pending Bookings Alert',
                "{$pendingCount} bookings are pending confirmation in the last 2 hours.",
                ['pending_count' => $pendingCount]
            );
            $this->info("Generated notification for {$pendingCount} pending bookings");
        }
    }

    private function checkHighVolumeBookings()
    {
        $todayBookings = Booking::whereDate('created_at', Carbon::today())->count();
        $avgDailyBookings = Booking::where('created_at', '>=', Carbon::now()->subDays(30))
            ->count() / 30;

        if ($todayBookings > ($avgDailyBookings * 1.5)) {
            $this->notificationService->sendAdminNotification(
                'booking_alert',
                'High Booking Volume Detected',
                "Today's booking volume ({$todayBookings}) is significantly higher than average ({$avgDailyBookings}).",
                ['today_bookings' => $todayBookings, 'avg_bookings' => $avgDailyBookings]
            );
            $this->info("Generated notification for high booking volume");
        }
    }

    private function checkCancelledBookings()
    {
        $recentCancellations = Booking::where('status', 'cancelled')
            ->where('updated_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($recentCancellations > 5) {
            $this->notificationService->sendAdminNotification(
                'booking_alert',
                'High Cancellation Rate Alert',
                "{$recentCancellations} bookings cancelled in the last hour. Please investigate.",
                ['cancellation_count' => $recentCancellations]
            );
            $this->info("Generated notification for high cancellation rate");
        }
    }

    private function checkInactiveOperators()
    {
        $inactiveOperators = User::role('operator')
            ->where('is_active', false)
            ->count();

        if ($inactiveOperators > 0) {
            $this->notificationService->sendAdminNotification(
                'operator_alert',
                'Inactive Operators Found',
                "{$inactiveOperators} operators are currently inactive and may need attention.",
                ['inactive_count' => $inactiveOperators]
            );
            $this->info("Generated notification for inactive operators");
        }
    }

    private function checkRevenueTargets()
    {
        $monthlyRevenue = Booking::where('status', 'confirmed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $target = 500000; // Rs. 500,000 monthly target

        if ($monthlyRevenue >= $target) {
            // Check if we already sent this notification this month
            $existingNotification = Notification::where('type', 'revenue_milestone')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->exists();

            if (!$existingNotification) {
                $this->notificationService->sendAdminNotification(
                    'revenue_milestone',
                    'Monthly Revenue Target Achieved!',
                    "Congratulations! Monthly revenue target of Rs. " . number_format($target) . " has been achieved with Rs. " . number_format($monthlyRevenue) . ".",
                    ['revenue' => $monthlyRevenue, 'target' => $target]
                );
                $this->info("Generated notification for revenue milestone");
            }
        }
    }

    private function checkSystemHealth()
    {
        // Check for system health indicators
        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $totalOperators = User::role('operator')->count();

        if ($totalUsers > 1000 && $totalBookings > 5000) {
            $this->notificationService->sendAdminNotification(
                'system_milestone',
                'Platform Growth Milestone',
                "Platform has reached {$totalUsers} users and {$totalBookings} total bookings!",
                ['users' => $totalUsers, 'bookings' => $totalBookings]
            );
            $this->info("Generated notification for platform growth");
        }
    }

    private function generateTestNotifications()
    {
        $this->info('Generating test notifications...');

        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $this->notificationService->sendNotification(
                $admin,
                'test_notification',
                'Test Notification',
                'This is a test notification generated by the system command.',
                ['generated_at' => now()],
                route('admin.dashboard'),
                'View Dashboard',
                'medium'
            );
        }

        $this->info('Test notifications generated for all admin users!');
    }
}
