<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Symfony\Component\HttpFoundation\Response;

class MarkNotificationsAsRead
{
    /**
     * Mark the notification a deep link was opened from as read.
     *
     * Any request carrying a `notify_id` marks that notification as read for the
     * authenticated user, so the client does not need an extra round trip.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $notificationId = $request->input('notify_id');

        if (auth('api')->check() && is_string($notificationId) && Str::isUuid($notificationId)) {
            app(NotificationService::class)->markAsRead(auth('api')->user(), ['ids' => [$notificationId]]);
        }

        return $next($request);
    }
}
