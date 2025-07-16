<?php

namespace App\Events;

use App\Models\SeatReservation;
use App\Models\Schedule;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatReserved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $reservation;
    public $schedule;
    public $newSeats;

    /**
     * Create a new event instance.
     */
    public function __construct(SeatReservation $reservation, Schedule $schedule, array $newSeats = [])
    {
        $this->reservation = $reservation;
        $this->schedule = $schedule;
        $this->newSeats = $newSeats;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('schedule.' . $this->schedule->id),
            new PrivateChannel('operator.' . $this->schedule->operator_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'user_id' => $this->reservation->user_id,
            'schedule_id' => $this->schedule->id,
            'seat_numbers' => $this->reservation->seat_numbers,
            'new_seats' => $this->newSeats,
            'expires_at' => $this->reservation->expires_at,
            'customer_name' => $this->reservation->user->name ?? 'Guest',
            'route_name' => $this->schedule->route->sourceCity->name . ' → ' . $this->schedule->route->destinationCity->name,
            'travel_date' => $this->schedule->travel_date->format('Y-m-d'),
            'departure_time' => $this->schedule->departure_time->format('H:i'),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'seat.reserved';
    }
}
