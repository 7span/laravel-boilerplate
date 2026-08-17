<?php

namespace App\Http\Middleware;

use Closure;
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
        $notifyId = $request->get('notify_id');

        if ($request->user() && is_string($notifyId) && $notifyId !== '') {
            app(NotificationService::class)->markAsRead($request->user(), ['ids' => [$notifyId]]);
        }

        return $next($request);
    }
}
