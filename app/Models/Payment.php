<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'payment_method',
        'transaction_id',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'gateway_data',
        'paid_at',
        'failed_at',
        'failure_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'gateway_data' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payment is successful.
     */
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted($transactionId = null, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'gateway_response' => $gatewayResponse ?? $this->gateway_response
        ]);

        // Update booking status
        $this->booking->update(['status' => 'confirmed']);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed($reason = null, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'gateway_response' => $gatewayResponse ?? $this->gateway_response
        ]);
    }
}
