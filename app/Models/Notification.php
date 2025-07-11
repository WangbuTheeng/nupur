<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'title',
        'message',
        'action_url',
        'action_text',
        'priority',
        'channel',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->forceFill(['read_at' => null])->save();
        }
    }

    /**
     * Determine if a notification has been read.
     */
    public function read(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     */
    public function unread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Scope to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to filter by notification type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by priority.
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
}
