<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\User\ChangeStatus;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @tags Admin / Users
 */
#[Group('Admin / Users', weight: 1)]
class UserStatusController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    /**
     * Change user status.
     *
     * Enables or disables a user account. Requires admin privileges.
     *
     * @response array{message: string, user: UserResource}
     */
    public function __invoke(User $user, ChangeStatus $request)
    {
        $user = $this->userService->changeStatus($user, $request->validated());

        return $this->success($user);
    }
}
