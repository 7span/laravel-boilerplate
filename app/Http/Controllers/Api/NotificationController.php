<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Http\Requests\Notification\OneSignalData;
use App\Http\Requests\Notification\Request as NotificationRequest;
use App\Http\Resources\Notification\Collection as NotificationCollection;

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
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
            new OA\Parameter(
                name: 'limit',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
            ),
            new OA\Parameter(
                name: 'include',
                in: 'query',
                description: 'Include :`user,sender`',
            ),
            new OA\Parameter(
                name: 'filter[is_read]',
                in: 'query',
                description: 'Pass `true` to get already read notification and `false` for unread notifications.'
            )
        ],
        responses: [
            new OA\Response(response: '200', description: 'Success'),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'ids',
                        type: 'array',
                        items: new OA\Items(type: 'integer'),
                        description: 'Array of notification IDs to mark as read.',
                        example: [1, 2, 3]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: '200', description: 'Success.'),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['onesignal_player_id'],
                properties: [
                    new OA\Property(
                        property: 'onesignal_player_id',
                        type: 'string',
                        description: "The OneSignal Player ID for push notifications.",
                        example: '1a2b3c4d5e'
                    ),
                    new OA\Property(
                        property: 'device_id',
                        type: 'string',
                        description: "The device ID for the user's device.",
                        example: 'device12345'
                    ),
                    new OA\Property(
                        property: 'device_type',
                        type: 'string',
                        description: "The type of device (e.g., 'android', 'ios').",
                        example: 'android'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Player ID successfully updated.',
            ),
            new OA\Response(
                response: '400',
                description: 'Validation errors occurred.'
            ),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function setOnesignalData(OneSignalData $request)
    {
        $data = $this->notificationService->setOnesignalData($request->validated());

        return $this->success($data);
    }
}