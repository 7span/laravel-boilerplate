<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\User\ChangeStatusRequest;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @tags User
 */
#[Group('User', weight: 70)]
class UserStatusController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly UserService $userService) {}

    /**
     * Change status.
     *
     * @response array{message: string, user: UserResource}
     */
    public function __invoke(User $user, ChangeStatusRequest $request): JsonResponse
    {
        $data = $this->userService->changeStatus($user, $request->validated());

        return $this->success($data);
    }
}
