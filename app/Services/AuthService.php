<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\WelcomeUser;
use App\Http\Resources\User\Resource as UserResource;

class AuthService
{
    private User $userObj;

    private UserService $userService;

    public function __construct()
    {
        $this->userObj = new User;
        $this->userService = new UserService;
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string, data: UserResource, token: string}
     */
    public function register(array $inputs): array
    {
        $createdUser = $this->userObj->create($inputs);
        $createdUser->assignRole(config('site.roles.user'));

        $user = $this->userService->resource($createdUser->id);

        $user->notify(new WelcomeUser);

        return [
            'message' => __('message.register_success'),
            'data' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];
    }
}
