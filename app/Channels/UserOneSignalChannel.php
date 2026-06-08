<?php

declare(strict_types=1);

namespace App\Channels;

use GuzzleHttp\Psr7\Response;
use Berkayk\OneSignal\OneSignalClient;
use Psr\Http\Message\ResponseInterface;
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

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification): ResponseInterface
    {
        if (! config('site.notification_enabled')) {
            // Return a dummy ResponseInterface (e.g., an empty response) if notification is disabled
            return new Response(204); // No Content
        }

        /** @var \App\Models\User $notifiable */
        $devices = $notifiable->userDevices();
        /** @var array<int, string> $userIds */
        $userIds = $devices->pluck('onesignal_player_id')->toArray();

        if (empty($userIds)) {
            // Return a dummy ResponseInterface if no user IDs
            return new Response(204); // No Content
        }

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $this->oneSignal->sendNotificationCustom(
            $this->payload($notifiable, $notification, $userIds)
        );

        if ($response->getStatusCode() !== 200) {
            /** @var \Throwable $exception */
            $exception = CouldNotSendNotification::serviceRespondedWithAnError($response);
            throw $exception;
        }

        return $response;
    }
}
