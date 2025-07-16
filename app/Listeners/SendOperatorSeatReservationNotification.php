<?php

namespace App\Listeners;

use App\Events\SeatReserved;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOperatorSeatReservationNotification implements ShouldQueue
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
    public function handle(SeatReserved $event): void
    {
        $reservation = $event->reservation;
        $schedule = $event->schedule;
        $newSeats = $event->newSeats;
        
        // Only send notification if there are new seats reserved
        if (empty($newSeats)) {
            return;
        }
        
        $operator = $schedule->operator;
        $customerName = $reservation->user->name ?? 'Guest Customer';
        $seatCount = count($newSeats);
        $seatList = implode(', ', $newSeats);
        $routeName = $schedule->route->sourceCity->name . ' → ' . $schedule->route->destinationCity->name;
        
        $title = 'Seats Reserved';
        $message = "{$customerName} has reserved {$seatCount} seat(s) ({$seatList}) for {$routeName} on {$schedule->travel_date->format('M d, Y')}.";
        
        $this->notificationService->sendNotification(
            $operator,
            'seat_reserved',
            $title,
            $message,
            [
                'reservation_id' => $reservation->id,
                'customer_name' => $customerName,
                'seat_numbers' => $newSeats,
                'total_seats' => count($reservation->seat_numbers),
                'route' => $routeName,
                'travel_date' => $schedule->travel_date->format('Y-m-d'),
                'departure_time' => $schedule->departure_time->format('H:i'),
                'expires_at' => $reservation->expires_at,
                'schedule_id' => $schedule->id,
            ],
            route('operator.schedules.show', $schedule),
            'View Schedule',
            'medium',
            ['database', 'realtime']
        );
    }
}
