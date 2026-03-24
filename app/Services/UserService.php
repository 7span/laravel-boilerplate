<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Libraries\MediaHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\Resource;

class UserService
{
    private User $userObj;

    public function __construct()
    {
        $this->userObj = new User;
    }

    public function resource(int $id): User
    {
        /** @var User $user */
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function update(int $id, array $inputs = []): array
    {
        $user = $this->resource($id);

        $user->update($inputs);

        /** @var string $profileTag */
        $profileTag = config('media.tags.profile');
        
        /** @var array<int|string, mixed> $mediaInput */
        $mediaInput = $inputs[$profileTag];

        $mediaId = MediaHelper::attachMedia($mediaInput);
        $user->syncMedia($mediaId, $profileTag);

        $data = [
            'message' => __('message.user_profile_update'),
            'user' => new Resource($user),
        ];

        return $data;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function changeStatus(User $user, array $inputs = []): array
    {
        $user->update($inputs);
        $data = [
            'message' => __('entity.entityUpdated', ['entity' => 'User status']),
            'user' => new Resource($user),
        ];

        return $data;
    }

    /**
     * @param array<string, mixed> $inputs
     * @return array<string, mixed>
     */
    public function changePassword(array $inputs): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            throw new \App\Exceptions\CustomException(__('auth.failed'));
        }

        $user->update([
            'password' => $inputs['password'],
        ]);

        $data['message'] = __('message.password_change_success');

        return $data;
    }
}
