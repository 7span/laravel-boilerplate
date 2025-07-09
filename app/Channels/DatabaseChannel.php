<?php

namespace App\Channels;

use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use App\Models\Notification as NotificationModel;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

class DatabaseChannel extends IlluminateDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $this->getData($notifiable, $notification);

        return NotificationModel::create([
            'id' => $notification->id,
            'user_id' => $notifiable->id,
            'sent_by' => $data['sent_by'] ?? Auth::id(),
            'title' => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'type' => $data['type'] ?? null,
            'notifiable_type' => $data['notifiable_type'] ?? null,
            'notifiable_id' => $data['notifiable_id'] ?? null,
            'data' => $data['data'] ?? null,
            'read_at' => null,
        ]);
    }
}