<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkNotificationsAsRead
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('sanctum')->check() && $request->has('notify_id')) {
            Notification::where('id', $request->get('notify_id'))->where('user_id', auth('sanctum')->id())->whereNull('read_at')->update(['read_at' => now()]);
        }

        return $next($request);
    }
}