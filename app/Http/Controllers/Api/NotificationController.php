<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\Notification\OneSignalData;
use App\Http\Requests\Notification\Request as NotificationRequest;
use App\Http\Resources\Notification\Collection as NotificationCollection;
use App\Models\Notification;
use App\OpenApi\Attributes\ApiModel;

class NotificationController extends Controller
{
    use ApiResponser;

    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService;
    }

    #[ApiModel(Notification::class)]
    #[OA\Get(
        path: '/api/notifications',
        operationId: 'notificationList',
        tags: ['Notification'],
        summary: 'Notification List',
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function index()
    {
        $data = $this->notificationService->collection();

        return $this->collection(new NotificationCollection($data));
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
}
