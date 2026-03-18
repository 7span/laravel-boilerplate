<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\UserService;
use OpenApi\Attributes as OA;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeStatus;
use App\Data\Request\User\ChangeStatusRequestData;

class UserStatusController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    #[OA\Post(
        path: '/api/admin/users/{id}/change-status',
        tags: ['Admin / User'],
        operationId: 'changeStatus',
        summary: 'Change user status',
        security: [[
            'bearerAuth' => [],
        ]],
    )]
    public function __invoke(User $user, ChangeStatus $request)
    {
        $requestData = ChangeStatusRequestData::fromRequest($request->validated());
        $user = $this->userService->changeStatus($user, $requestData->toArray());

        return $this->success($user);
    }
}
