<?php

namespace App\Listeners;

use App\Events\BookingStatusUpdated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOperatorNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(BookingStatusUpdated $event): void
    {
        $booking = $event->booking;
        
        // Only send notification for new bookings (pending status)
        if ($booking->status === 'pending' && $booking->wasRecentlyCreated) {
            $this->notificationService->sendOperatorBookingNotification($booking);
        }
        
        // Send notification for status changes
        if ($booking->wasChanged('status') && !$booking->wasRecentlyCreated) {
            $this->sendStatusChangeNotification($booking);
        }
    }

    /**
     * Send notification for booking status changes.
     */
    private function sendStatusChangeNotification($booking)
    {
        $operator = $booking->schedule->operator;
        $status = $booking->status;
        
        $titles = [
            'confirmed' => 'Booking Confirmed',
            'cancelled' => 'Booking Cancelled',
            'completed' => 'Trip Completed',
        ];
        
        $messages = [
            'confirmed' => "Booking {$booking->booking_reference} has been confirmed and payment received.",
            'cancelled' => "Booking {$booking->booking_reference} has been cancelled by the customer.",
            'completed' => "Trip for booking {$booking->booking_reference} has been completed.",
        ];
        
        if (isset($titles[$status])) {
            $this->notificationService->sendNotification(
                $operator,
                'booking_' . $status,
                $titles[$status],
                $messages[$status],
                [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'status' => $status,
                    'route' => $booking->schedule->route->full_name ?? 'N/A',
                ],
                route('operator.bookings.show', $booking),
                'View Booking',
                $status === 'cancelled' ? 'high' : 'medium',
                ['database', 'realtime']
            );
        }
    }
}
