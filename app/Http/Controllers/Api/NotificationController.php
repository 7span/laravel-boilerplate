<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\Notification\OneSignalData;
use App\Http\Requests\Notification\Request as NotificationRequest;
use App\Http\Resources\Notification\Resource as NotificationResource;

class NotificationController extends Controller
{
    use ApiResponser;

    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService;
    }

    #[OA\Get(
        path: '/api/notifications',
        operationId: 'notificationList',
        tags: ['Notification'],
        summary: 'Notification List',
        x: ['model' => Notification::class],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function index()
    {
        $data = $this->notificationService->collection();

        return NotificationResource::collection($data);
    }

    #[OA\Post(
        path: '/api/notifications/read',
        operationId: 'readAllNotifications',
        tags: ['Notification'],
        summary: 'Mark notifications as read',
        description: 'Allows marking all notifications or specific notifications as read for the authenticated user.',
        security: [['bearerAuth' => []]]
    )]
    public function readAllNotification(NotificationRequest $request)
    {
        $data = $this->notificationService->readAllNotification($request->validated());

        return $data;
    }

    #[OA\Post(
        path: '/api/notifications/unread',
        operationId: 'unreadNotifications',
        tags: ['Notification'],
        summary: 'Mark notifications as unread',
        description: 'Allows marking all notifications or specific notifications as unread for the authenticated user.',
        security: [['bearerAuth' => []]]
    )]
    public function markAsUnread(NotificationRequest $request)
    {
        $data = $this->notificationService->markAsUnread($request->validated());

        return $data;
    }

    #[OA\Post(
        path: '/api/onesignal-player-id',
        operationId: 'setOnesignalPlayerId',
        tags: ['Notification'],
        description: 'Set OneSignal player ID for push notifications.',
        security: [['bearerAuth' => []]]
    )]
    public function setOnesignalData(OneSignalData $request)
    {
        $data = $this->notificationService->setOnesignalData($request->validated());

        return $this->success($data);
    }

    #[OA\Get(
        path: '/api/notifications/unread-count',
        operationId: 'unreadNotificationCount',
        tags: ['Notification'],
        summary: 'Get unread notifications count',
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function unreadCount(): JsonResponse
    {
        $data = $this->notificationService->unreadCount();

        return $this->success($data);
    }
}
