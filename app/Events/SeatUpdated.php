<?php

namespace App\Events;

use App\Models\Schedule;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $seatNumber;
    public $status; // 'booked', 'available', 'reserved'
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Schedule $schedule, $seatNumber, $status, $userId = null)
    {
        $this->schedule = $schedule;
        $this->seatNumber = $seatNumber;
        $this->status = $status;
        $this->userId = $userId;
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
            'schedule_id' => $this->schedule->id,
            'seat_number' => $this->seatNumber,
            'status' => $this->status,
            'user_id' => $this->userId,
            'available_seats' => $this->schedule->available_seats,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'seat.updated';
    }
}
