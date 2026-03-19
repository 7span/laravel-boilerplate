<?php

namespace App\Http\Resources\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Media\Resource as MediaResource;
use App\Http\Resources\UserDevice\Resource as UserDeviceResource;

/**
 * @property User $resource
 */
#[SchemaName('User')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = User::class;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the user.
             */
            'id' => $this->id,
            /**
             * The user's first name.
             */
            'first_name' => $this->first_name,
            /**
             * The user's last name.
             */
            'last_name' => $this->last_name,
            /**
             * The full display name of the user.
             */
            'name' => $this->name,
            /**
             * The user's unique username.
             */
            'username' => $this->username,
            /**
             * The user's email address.
             *
             * @format email
             */
            'email' => $this->email,
            /**
             * The user's account status.
             */
            'status' => $this->status,
            /**
             * Human-readable label for the account status.
             */
            'display_status' => $this->display_status,
            /**
             * The country dial code (e.g. +1).
             */
            'country_code' => $this->country_code,
            /**
             * The user's mobile number.
             */
            'mobile_no' => $this->mobile_no,
            /**
             * Mobile number with country code prefix.
             */
            'display_mobile_no' => $this->display_mobile_no,
            /**
             * Timestamp when the email was verified.
             */
            'email_verified_at' => $this->email_verified_at,
            /**
             * Timestamp of the user's last login.
             */
            'last_login_at' => $this->last_login_at,
            /**
             * Timestamp when the user was created.
             */
            'created_at' => $this->created_at,
            'profile_image' => new MediaResource($this->whenLoadedMedia(config('media.tags.profile'), true)),
            'user_device' => new UserDeviceResource($this->whenLoaded('userDevice')),
        ];
    }
}
