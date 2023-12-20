<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeStatus;

/**
 * @tags User
 */
class UserStatusController extends Controller
{
    use ApiResponser;

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * User Change Status
     */
    public function __invoke(ChangeStatus $inputs)
    {
        $data = $this->userService->changeStatus($inputs);

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
