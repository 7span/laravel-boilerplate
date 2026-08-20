<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Services\NotificationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Requests\Notification\OneSignalDataRequest;
use App\Http\Requests\Notification\MarkNotificationRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;
use App\Http\Resources\Notification\Resource as NotificationResource;

/**
 * @tags Notification
 */
#[Group('Notification', weight: 40)]
class NotificationController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly NotificationService $notificationService) {}

    /**
     * List notifications.
     *
     * @response AnonymousResourceCollection<LengthAwarePaginator<NotificationResource>>
     */
    public function index(): ResourceCollection
    {
        $data = $this->notificationService->collection(auth()->user());

        return $this->collection(NotificationResource::collection($data));
    }

    /**
     * Mark as read.
     *
     * Marks every unread notification of the authenticated user, or only the given ids.
     *
     * @response array{message: string}
     */
    public function markAsRead(MarkNotificationRequest $request): JsonResponse
    {
        $data = $this->notificationService->markAsRead(auth()->user(), $request->validated());

        return $this->success($data);
    }

    /**
     * Mark as unread.
     *
     * Marks every read notification of the authenticated user, or only the given ids.
     *
     * @response array{message: string}
     */
    public function markAsUnread(MarkNotificationRequest $request): JsonResponse
    {
        $data = $this->notificationService->markAsUnread(auth()->user(), $request->validated());

        return $this->success($data);
    }

    /**
     * Save OneSignal player id.
     *
     * @response array{message: string, data: UserDeviceResource}
     */
    public function setOnesignalData(OneSignalDataRequest $request): JsonResponse
    {
        $data = $this->notificationService->setOnesignalData(auth()->user(), $request->validated());

        return $this->success($data);
    }

    /**
     * Unread count.
     *
     * @response array{unread_count: int}
     */
    public function unreadCount(): JsonResponse
    {
        $data = $this->notificationService->unreadCount(auth()->user());

        return $this->success($data);
    }
}
