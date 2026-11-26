<?php

namespace App\Channels;

use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use App\Models\Notification as NotificationModel;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

/**
 * Persists notifications into the application's own `notifications` table, which
 * carries a title, description, sender and an optional related record on top of
 * the payload the framework stores.
 */
class DatabaseChannel extends IlluminateDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     */
    public function send($notifiable, Notification $notification): NotificationModel
    {
        $data = $this->getData($notifiable, $notification);

        return NotificationModel::create([
            'id' => $notification->id,
            'user_id' => $notifiable->id,
            'sent_by' => $data['sent_by'] ?? Auth::id(),
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? $notification::class,
            'notifiable_type' => $data['notifiable_type'] ?? null,
            'notifiable_id' => $data['notifiable_id'] ?? null,
            'data' => $data['data'] ?? null,
        ]);
    }
}
