<?php

namespace App\Channels;

use GuzzleHttp\Psr7\Response;
use Berkayk\OneSignal\OneSignalClient;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\Exceptions\CouldNotSendNotification;

class UserOneSignalChannel extends OneSignalChannel
{
    /** @var array<string, OneSignalClient> */
    private array $clients = [];

    public function __construct()
    {
        // The credentials depend on the notifiable, so the client is set in send().
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
            return new Response(204);
        }

        $userIds = $notifiable->routeNotificationFor('OneSignal', $notification);

        if (empty($userIds)) {
            return new Response(204);
        }

        $this->oneSignal = $this->clientFor($notifiable, $notification);

        return parent::send($notifiable, $notification);
    }

    /**
     * Resolve the client of the OneSignal app this notification is sent through, once per app.
     *
     * A notification may declare `public string $oneSignalApp` to pick the app itself,
     * which a user holding more than one role needs; otherwise the notifiable decides.
     *
     * @param  mixed  $notifiable
     */
    private function clientFor($notifiable, Notification $notification): OneSignalClient
    {
        $app = property_exists($notification, 'oneSignalApp')
            ? $notification->oneSignalApp
            : $notifiable->routeNotificationFor('OneSignalApp', $notification) ?? config('site.roles.user');

        return $this->clients[$app] ??= new OneSignalClient(
            config("services.onesignal.apps.{$app}.app_id"),
            config("services.onesignal.apps.{$app}.api_key"),
            null
        );
    }
}
