<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\UserService;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeStatus;

class UserStatusController extends Controller
{
    use ApiResponser;

    private $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    #[OA\Post(
        path: '/api/v1/admin/users/{id}/change-status',
        tags: ['Admin / User'],
        operationId: 'changeStatus',
        summary: 'Change user status',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID of the user to update',
            ),
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
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['status'],
                properties: [
                    new OA\Property(
                        property: 'status',
                        type: 'string',
                        description: 'New status of the user'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Status updated successfully',
            ),
            new OA\Response(
                response: 400,
                description: 'Bad Request'
            ),
        ],
        security: [[
            'bearerAuth' => [],
        ]],
    )]
    public function __invoke(User $user, ChangeStatus $request)
    {
        $user = $this->userService->changeStatus($user, $request->validated());

        return $this->success($user);
    }
}
