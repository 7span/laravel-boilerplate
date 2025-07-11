<?php

namespace App\Channels;

use Berkayk\OneSignal\OneSignalClient;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\Exceptions\CouldNotSendNotification;

class UserOneSignalChannel extends OneSignalChannel
{
    public function __construct()
    {
        $oneSignal = new OneSignalClient(
            config('site.onesignal.app_id'),
            config('site.onesignal.api_key'),
            null
        );
        parent::__construct($oneSignal);
    }

    public function send($notifiable, Notification $notification)
    {
        if (!config('site.notification_enabled')) {
            return;
        }

        $userIds = $notifiable->user_devices()->pluck('onesignal_player_id')->toArray();

        if (empty($userIds)) {
            return;
        }

        $response = $this->oneSignal->sendNotificationCustom(
            $this->payload($notifiable, $notification, $userIds)
        );

        if ($response->getStatusCode() !== 200) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($response);
        }

        return $response;
    }
}
