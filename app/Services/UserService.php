<?php

declare(strict_types = 1);

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

    public function resource(int $id, array $inputs = [])
    {
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }

    public function update(int $id, array $inputs = []): array
    {
        $user = $this->resource($id);

        $user->update($inputs);

        $mediaId = MediaHelper::attachMedia($inputs[config('media.tags.profile')]);
        $user->syncMedia($mediaId, config('media.tags.profile'));

        $data = [
            'message' => __('message.user_profile_update'),
            'user' => new Resource($user),
        ];

        return $data;
    }

    public function changeStatus(object $user, array $inputs = [])
    {
        $user->update($inputs);
        $data = [
            'message' => __('entity.entityUpdated', ['entity' => 'User status']),
            'user' => new Resource($user),
        ];

        return $data;
    }

    public function changePassword(array $inputs): array
    {
        $user = Auth::user();

        $user->update([
            'password' => $inputs['password'],
        ]);

        $data['message'] = __('message.password_change_success');

        return $data;
    }
}
