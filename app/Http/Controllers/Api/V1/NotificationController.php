<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Services\NotificationService;
use Dedoc\Scramble\Attributes\QueryParameter;
use App\Http\Requests\Notification\OneSignalData;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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

    public function __construct(private NotificationService $notificationService) {}

    /**
     * List.
     */
    #[QueryParameter('appends')]
    public function index(): AnonymousResourceCollection
    {
        $data = $this->notificationService->collection();

        return NotificationResource::collection($data);
    }

    /**
     * Mark read.
     *
     * @response array{message: string}
     */
    public function readAllNotification(NotificationRequest $request): array
    {
        $data = $this->notificationService->readAllNotification($request->validated());

        return $data;
    }

    /**
     * Mark unread.
     *
     * @response array{message: string}
     */
    public function markAsUnread(NotificationRequest $request): array
    {
        $data = $this->notificationService->markAsUnread($request->validated());

        return $data;
    }

    /**
     * Save OneSignal.
     *
     * @response array{message: string, data: UserDeviceResource}
     */
    public function setOnesignalData(OneSignalData $request): JsonResponse
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
