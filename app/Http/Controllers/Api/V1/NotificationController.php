<?php

<<<<<<< HEAD:app/Http/Controllers/Api/NotificationController.php
declare(strict_types=1);

namespace App\Http\Controllers\Api;
=======
namespace App\Http\Controllers\Api\V1;
>>>>>>> origin/master:app/Http/Controllers/Api/V1/NotificationController.php

use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Services\NotificationService;
use Dedoc\Scramble\Attributes\QueryParameter;
use App\Http\Requests\Notification\OneSignalData;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;
use App\Http\Requests\Notification\Request as NotificationRequest;
use App\Http\Resources\Notification\Resource as NotificationResource;

/**
 * @tags Notification
 */
#[Group('Notification', weight: 40)]
class NotificationController extends Controller
{
    use ApiResponser;

    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService;
    }

<<<<<<< HEAD:app/Http/Controllers/Api/NotificationController.php
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
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
=======
    /**
     * List.
     */
    #[QueryParameter('appends')]
    public function index()
>>>>>>> origin/master:app/Http/Controllers/Api/V1/NotificationController.php
    {
        $data = $this->notificationService->collection();

        return NotificationResource::collection($data);
    }

<<<<<<< HEAD:app/Http/Controllers/Api/NotificationController.php
    #[OA\Post(
        path: '/api/notifications/read',
        operationId: 'readAllNotifications',
        tags: ['Notification'],
        summary: 'Mark notifications as read',
        description: 'Allows marking all notifications or specific notifications as read for the authenticated user.',
        security: [['bearerAuth' => []]]
    )]
    public function readAllNotification(NotificationRequest $request): \Illuminate\Http\JsonResponse
=======
    /**
     * Mark read.
     *
     * @response array{message: string}
     */
    public function readAllNotification(NotificationRequest $request)
>>>>>>> origin/master:app/Http/Controllers/Api/V1/NotificationController.php
    {
        $data = $this->notificationService->readAllNotification($request->validated());

        return $this->success($data);
    }

<<<<<<< HEAD:app/Http/Controllers/Api/NotificationController.php
    #[OA\Post(
        path: '/api/onesignal-player-id',
        operationId: 'setOnesignalPlayerId',
        tags: ['Notification'],
        description: 'Set OneSignal player ID for push notifications.',
        security: [['bearerAuth' => []]]
    )]
    public function setOnesignalData(OneSignalData $request): \Illuminate\Http\JsonResponse
=======
    /**
     * Mark unread.
     *
     * @response array{message: string}
     */
    public function markAsUnread(NotificationRequest $request)
    {
        $data = $this->notificationService->markAsUnread($request->validated());

        return $data;
    }

    /**
     * Save OneSignal.
     *
     * @response array{message: string, data: UserDeviceResource}
     */
    public function setOnesignalData(OneSignalData $request)
>>>>>>> origin/master:app/Http/Controllers/Api/V1/NotificationController.php
    {
        $data = $this->notificationService->setOnesignalData($request->validated());

        return $this->success($data);
    }

    /**
     * Unread count.
     *
     * @response array{unread_count: int}
     */
    public function unreadCount(): JsonResponse
    {
        $data = $this->notificationService->unreadCount();

        return $this->success($data);
    }
}
