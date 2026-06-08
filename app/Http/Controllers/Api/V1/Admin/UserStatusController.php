<?php

<<<<<<< HEAD:app/Http/Controllers/Api/Admin/UserStatusController.php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;
=======
namespace App\Http\Controllers\Api\V1\Admin;
>>>>>>> origin/master:app/Http/Controllers/Api/V1/Admin/UserStatusController.php

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\UserService;
use App\Http\Controllers\Controller;
<<<<<<< HEAD:app/Http/Controllers/Api/Admin/UserStatusController.php
use Illuminate\Http\JsonResponse;
=======
use Dedoc\Scramble\Attributes\Group;
>>>>>>> origin/master:app/Http/Controllers/Api/V1/Admin/UserStatusController.php
use App\Http\Requests\User\ChangeStatus;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @tags Admin / User
 */
#[Group('Admin / User', weight: 70)]
class UserStatusController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

<<<<<<< HEAD:app/Http/Controllers/Api/Admin/UserStatusController.php
    #[OA\Post(
        path: '/api/admin/users/{id}/change-status',
        tags: ['Admin / User'],
        operationId: 'changeStatus',
        summary: 'Change user status',
        security: [[
            'bearerAuth' => [],
        ]],
    )]
    public function __invoke(User $user, ChangeStatus $request): \Illuminate\Http\JsonResponse
=======
    /**
     * Change status.
     *
     * @response array{message: string, user: UserResource}
     */
    public function __invoke(User $user, ChangeStatus $request)
>>>>>>> origin/master:app/Http/Controllers/Api/V1/Admin/UserStatusController.php
    {
        $user = $this->userService->changeStatus($user, $request->validated());

        return $this->success($user);
    }
}
