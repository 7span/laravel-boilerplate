<?php

namespace App\Services;

use App\Models\User;
use App\Libraries\MediaHelper;
use Illuminate\Support\Facades\App;
use App\Http\Resources\User\Resource as UserResource;

class UserService
{
    private User $userObj;

    public function __construct()
    {
        $this->userObj = new User;
    }

    /**
     * Fetch a user through the query builder so `include`, `fields` and `appends` apply.
     */
    public function resource(int $id): User
    {
        return $this->userObj->getQB()->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string, user: UserResource}
     */
    public function update(int $id, array $inputs): array
    {
        $user = $this->userObj->findOrFail($id);

        $profileTag = config('media.tags.profile');

        if (isset($inputs[$profileTag])) {
            $user->syncMedia(MediaHelper::attachMedia($inputs[$profileTag]), $profileTag);
        }

        unset($inputs[$profileTag]);

        $user->update($inputs);

        if (isset($inputs['locale'])) {
            App::setLocale($inputs['locale']);
        }

        return [
            'message' => __('message.user_profile_update'),
            'user' => new UserResource($this->resource($user->id)),
        ];
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string}
     */
    public function changePassword(User $user, array $inputs): array
    {
        $user->update(['password' => $inputs['password']]);

        return ['message' => __('message.password_change_success')];
    }

    /**
     * @param  array<string, mixed>  $inputs
     * @return array{message: string}
     */
    public function updateLocale(User $user, array $inputs): array
    {
        $user->update(['locale' => $inputs['locale']]);

        App::setLocale($inputs['locale']);

        return ['message' => __('entity.entityUpdated', ['entity' => 'Language'])];
    }
}
