<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin users
        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            $this->command->error('No admin users found. Please run AdminUserSeeder first.');
            return;
        }

        $sampleNotifications = [
            [
                'type' => 'booking_alert',
                'title' => 'High Volume Booking Alert',
                'message' => 'Unusual high booking volume detected for Kathmandu-Pokhara route. Consider adding more schedules.',
                'action_url' => '/admin/bookings',
                'action_text' => 'View Bookings',
                'priority' => 'high'
            ],
            [
                'type' => 'operator_registration',
                'title' => 'New Operator Registration',
                'message' => 'Himalayan Express has submitted registration documents for review.',
                'action_url' => '/admin/operators',
                'action_text' => 'Review Application',
                'priority' => 'medium'
            ],
            [
                'type' => 'system_alert',
                'title' => 'System Maintenance Scheduled',
                'message' => 'Scheduled maintenance will begin tonight at 2:00 AM. Expected downtime: 30 minutes.',
                'priority' => 'low'
            ],
            [
                'type' => 'revenue_milestone',
                'title' => 'Monthly Revenue Target Achieved',
                'message' => 'Congratulations! This month\'s revenue target of Rs. 500,000 has been achieved.',
                'action_url' => '/admin/reports/revenue',
                'action_text' => 'View Revenue Report',
                'priority' => 'medium'
            ],
            [
                'type' => 'booking_alert',
                'title' => 'Cancelled Booking Spike',
                'message' => '15 bookings cancelled in the last hour. Please investigate potential issues.',
                'action_url' => '/admin/bookings?status=cancelled',
                'action_text' => 'View Cancelled Bookings',
                'priority' => 'high'
            ],
            [
                'type' => 'operator_alert',
                'title' => 'Operator License Expiring',
                'message' => 'Kathmandu Express license expires in 30 days. Renewal required.',
                'action_url' => '/admin/operators',
                'action_text' => 'View Operator Details',
                'priority' => 'medium'
            ],
            [
                'type' => 'system_alert',
                'title' => 'Database Backup Completed',
                'message' => 'Daily database backup completed successfully at 3:00 AM.',
                'priority' => 'low'
            ],
            [
                'type' => 'user_alert',
                'title' => 'New User Registrations',
                'message' => '25 new users registered today. User growth is trending upward.',
                'action_url' => '/admin/users',
                'action_text' => 'View Users',
                'priority' => 'low'
            ],
            [
                'type' => 'booking_alert',
                'title' => 'Payment Gateway Issue',
                'message' => 'eSewa payment gateway experiencing intermittent issues. Monitor closely.',
                'action_url' => '/admin/bookings?payment_status=failed',
                'action_text' => 'View Failed Payments',
                'priority' => 'high'
            ],
            [
                'type' => 'route_alert',
                'title' => 'Popular Route Performance',
                'message' => 'Kathmandu-Chitwan route showing 95% occupancy rate. Consider adding more buses.',
                'action_url' => '/admin/routes',
                'action_text' => 'View Routes',
                'priority' => 'medium'
            ]
        ];

        foreach ($admins as $admin) {
            // Create 5-7 random notifications for each admin
            $notificationCount = rand(5, 7);
            $selectedNotifications = array_rand($sampleNotifications, $notificationCount);
            
            if (!is_array($selectedNotifications)) {
                $selectedNotifications = [$selectedNotifications];
            }

            foreach ($selectedNotifications as $index) {
                $notification = $sampleNotifications[$index];
                
                // Some notifications should be read, others unread
                $isRead = rand(0, 100) < 40; // 40% chance of being read
                
                Notification::create([
                    'type' => $notification['type'],
                    'notifiable_type' => get_class($admin),
                    'notifiable_id' => $admin->id,
                    'data' => [],
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'action_url' => $notification['action_url'] ?? null,
                    'action_text' => $notification['action_text'] ?? null,
                    'priority' => $notification['priority'],
                    'channel' => 'database',
                    'sent_at' => now()->subMinutes(rand(5, 1440)), // Random time in last 24 hours
                    'read_at' => $isRead ? now()->subMinutes(rand(1, 720)) : null, // Random read time if read
                    'created_at' => now()->subMinutes(rand(5, 1440)),
                    'updated_at' => now()->subMinutes(rand(1, 720)),
                ]);
            }
        }

        $this->command->info('Sample notifications created for admin users.');
    }
}
