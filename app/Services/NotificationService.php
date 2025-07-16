<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to a user.
     */
    public function sendNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = [],
        string $actionUrl = null,
        string $actionText = null,
        string $priority = 'medium',
        array $channels = ['database']
    ) {
        try {
            // Create notification record
            $notification = Notification::create([
                'type' => $type,
                'notifiable_type' => get_class($user),
                'notifiable_id' => $user->id,
                'data' => $data,
                'title' => $title,
                'message' => $message,
                'action_url' => $actionUrl,
                'action_text' => $actionText,
                'priority' => $priority,
                'channel' => in_array('database', $channels) ? 'database' : $channels[0],
                'sent_at' => now(),
            ]);

            // Fire real-time event
            if (in_array('realtime', $channels) || in_array('database', $channels)) {
                event(new NotificationSent($notification));
            }

            // Send email if requested
            if (in_array('email', $channels)) {
                $this->sendEmailNotification($user, $notification);
            }

            // Send SMS if requested
            if (in_array('sms', $channels)) {
                $this->sendSmsNotification($user, $notification);
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Send booking confirmation notification.
     */
    public function sendBookingConfirmation($booking)
    {
        return $this->sendNotification(
            $booking->user,
            'booking_confirmed',
            'Booking Confirmed',
            "Your booking {$booking->booking_reference} has been confirmed for {$booking->schedule->route->full_name}.",
            [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'route' => $booking->schedule->route->full_name,
                'travel_date' => $booking->schedule->travel_date->format('Y-m-d'),
                'departure_time' => $booking->schedule->departure_time->format('H:i'),
            ],
            route('booking.show', $booking),
            'View Booking',
            'high',
            ['database', 'realtime', 'email']
        );
    }

    /**
     * Send payment received notification.
     */
    public function sendPaymentReceived($booking)
    {
        return $this->sendNotification(
            $booking->user,
            'payment_received',
            'Payment Received',
            "Payment of Rs. {$booking->total_amount} received for booking {$booking->booking_reference}.",
            [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'amount' => $booking->total_amount,
            ],
            route('booking.show', $booking),
            'View Booking',
            'medium',
            ['database', 'realtime']
        );
    }

    /**
     * Send schedule cancelled notification.
     */
    public function sendScheduleCancelled($schedule, $reason = null)
    {
        $bookings = $schedule->bookings()->where('status', '!=', 'cancelled')->get();
        
        foreach ($bookings as $booking) {
            $message = "Your trip on {$schedule->route->full_name} scheduled for {$schedule->travel_date->format('M d, Y')} has been cancelled.";
            if ($reason) {
                $message .= " Reason: {$reason}";
            }

            $this->sendNotification(
                $booking->user,
                'schedule_cancelled',
                'Trip Cancelled',
                $message,
                [
                    'schedule_id' => $schedule->id,
                    'booking_id' => $booking->id,
                    'route' => $schedule->route->full_name,
                    'travel_date' => $schedule->travel_date->format('Y-m-d'),
                    'reason' => $reason,
                ],
                route('booking.show', $booking),
                'View Booking',
                'high',
                ['database', 'realtime', 'email', 'sms']
            );
        }
    }

    /**
     * Send seat availability alert.
     */
    public function sendSeatAvailabilityAlert($user, $schedule)
    {
        return $this->sendNotification(
            $user,
            'seat_available',
            'Seat Available',
            "A seat is now available on {$schedule->route->full_name} for {$schedule->travel_date->format('M d, Y')}.",
            [
                'schedule_id' => $schedule->id,
                'route' => $schedule->route->full_name,
                'travel_date' => $schedule->travel_date->format('Y-m-d'),
                'available_seats' => $schedule->available_seats,
            ],
            route('booking.create', ['schedule' => $schedule->id]),
            'Book Now',
            'medium',
            ['database', 'realtime']
        );
    }

    /**
     * Send booking reminder notification.
     */
    public function sendBookingReminder($booking, $hoursBeforeDeparture = 24)
    {
        return $this->sendNotification(
            $booking->user,
            'booking_reminder',
            'Trip Reminder',
            "Reminder: Your trip {$booking->booking_reference} departs in {$hoursBeforeDeparture} hours.",
            [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'route' => $booking->schedule->route->full_name,
                'departure_time' => $booking->schedule->departure_time->format('H:i'),
                'hours_before' => $hoursBeforeDeparture,
            ],
            route('booking.show', $booking),
            'View Booking',
            'medium',
            ['database', 'realtime', 'sms']
        );
    }

    /**
     * Send operator notification for new booking.
     */
    public function sendOperatorBookingNotification($booking)
    {
        $operator = $booking->schedule->operator;
        $customerName = $booking->user->name ?? 'Guest Customer';
        $routeName = $booking->schedule->route->sourceCity->name . ' → ' . $booking->schedule->route->destinationCity->name;
        $seatList = implode(', ', $booking->seat_numbers);

        $title = $booking->status === 'confirmed' ? 'New Booking Confirmed' : 'New Booking Received';
        $statusText = $booking->status === 'confirmed' ? 'confirmed and paid' : 'received';

        $message = "{$customerName} has {$statusText} booking {$booking->booking_reference} for {$routeName}. Seats: {$seatList}";

        return $this->sendNotification(
            $operator,
            'new_booking',
            $title,
            $message,
            [
                'booking_id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'customer_name' => $customerName,
                'passenger_count' => $booking->passenger_count,
                'total_amount' => $booking->total_amount,
                'seat_numbers' => $booking->seat_numbers,
                'route' => $routeName,
                'travel_date' => $booking->schedule->travel_date->format('Y-m-d'),
                'departure_time' => $booking->schedule->departure_time->format('H:i'),
                'status' => $booking->status,
                'booking_type' => $booking->booking_type,
            ],
            route('operator.bookings.show', $booking),
            'View Booking',
            $booking->status === 'confirmed' ? 'high' : 'medium',
            ['database', 'realtime']
        );
    }

    /**
     * Send seat reservation expiry notification.
     */
    public function sendSeatReservationExpiry($reservation)
    {
        $schedule = $reservation->schedule;
        $minutesLeft = now()->diffInMinutes($reservation->expires_at);

        return $this->sendNotification(
            $reservation->user,
            'seat_reservation_expiry',
            'Seat Reservation Expiring Soon',
            "Your seat reservation for {$schedule->route->full_name} will expire in {$minutesLeft} minutes. Complete your booking to secure your seats.",
            [
                'reservation_id' => $reservation->id,
                'schedule_id' => $schedule->id,
                'seat_numbers' => $reservation->seat_numbers,
                'route' => $schedule->route->full_name,
                'travel_date' => $schedule->travel_date->format('Y-m-d'),
                'departure_time' => $schedule->departure_time->format('H:i'),
                'expires_at' => $reservation->expires_at,
                'minutes_left' => $minutesLeft,
            ],
            route('booking.seat-selection', $schedule),
            'Complete Booking',
            'high',
            ['database', 'realtime', 'sms']
        );
    }

    /**
     * Send admin notification for system events.
     */
    public function sendAdminNotification($type, $title, $message, $data = [])
    {
        $admins = User::role('admin')->get();
        
        foreach ($admins as $admin) {
            $this->sendNotification(
                $admin,
                $type,
                $title,
                $message,
                $data,
                null,
                null,
                'medium',
                ['database', 'realtime']
            );
        }
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('notifiable_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Get unread notifications for user.
     */
    public function getUnreadNotifications($user, $limit = 10)
    {
        return Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Send email notification (placeholder).
     */
    private function sendEmailNotification($user, $notification)
    {
        // TODO: Implement email sending logic
        Log::info('Email notification sent', [
            'user_id' => $user->id,
            'notification_id' => $notification->id,
            'type' => $notification->type
        ]);
    }

    /**
     * Send SMS notification (placeholder).
     */
    private function sendSmsNotification($user, $notification)
    {
        // TODO: Implement SMS sending logic
        Log::info('SMS notification sent', [
            'user_id' => $user->id,
            'notification_id' => $notification->id,
            'type' => $notification->type
        ]);
    }
}
