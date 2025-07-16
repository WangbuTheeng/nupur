<?php

namespace App\Listeners;

use App\Events\SeatReservationExpired;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOperatorSeatExpiryNotification implements ShouldQueue
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
    public function handle(SeatReservationExpired $event): void
    {
        $reservation = $event->reservation;
        $schedule = $event->schedule;
        $seatNumbers = $event->seatNumbers;
        
        $operator = $schedule->operator;
        $customerName = $reservation->user->name ?? 'Guest Customer';
        $seatCount = count($seatNumbers);
        $seatList = implode(', ', $seatNumbers);
        $routeName = $schedule->route->sourceCity->name . ' → ' . $schedule->route->destinationCity->name;
        
        $title = 'Seat Reservation Expired';
        $message = "{$customerName}'s reservation for {$seatCount} seat(s) ({$seatList}) on {$routeName} has expired. Seats are now available for booking.";
        
        $this->notificationService->sendNotification(
            $operator,
            'seat_reservation_expired',
            $title,
            $message,
            [
                'reservation_id' => $reservation->id,
                'customer_name' => $customerName,
                'seat_numbers' => $seatNumbers,
                'seat_count' => $seatCount,
                'route' => $routeName,
                'travel_date' => $schedule->travel_date->format('Y-m-d'),
                'departure_time' => $schedule->departure_time->format('H:i'),
                'schedule_id' => $schedule->id,
            ],
            route('operator.schedules.show', $schedule),
            'View Schedule',
            'low',
            ['database', 'realtime']
        );
    }
}
