<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Services\NotificationService;
use App\Http\Requests\Notification\OneSignalData;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;
use App\Http\Requests\Notification\Request as NotificationRequest;
use App\Http\Resources\Notification\Resource as NotificationResource;

/**
 * @tags Notifications
 */
#[Group('Notifications', weight: 6)]
class NotificationController extends Controller
{
    use ApiResponser;

    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService;
    }

    /**
     * List notifications.
     *
     * Returns a paginated list of in-app notifications for the authenticated user.
     */
    public function index()
    {
        $data = $this->notificationService->collection();

        return NotificationResource::collection($data);
    }

    /**
     * Mark notifications as read.
     *
     * Marks all or specific notifications as read for the authenticated user.
     *
     * @response array{message: string}
     */
    public function readAllNotification(NotificationRequest $request)
    {
        $data = $this->notificationService->readAllNotification($request->validated());

        return $data;
    }

    /**
     * Register push notification device.
     *
     * Registers or updates the OneSignal player ID for the authenticated user's device.
     *
     * @response array{message: string, data: UserDeviceResource}
     */
    public function setOnesignalData(OneSignalData $request)
    {
        $data = $this->notificationService->setOnesignalData($request->validated());

        return $this->success($data);
    }
}
