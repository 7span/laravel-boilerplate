<?php

declare(strict_types = 1);

namespace App\Data\Response;

use App\Data\Response\MediaData;
use App\Enums\UserStatus;
use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

final class UserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $username,
        public readonly string $email,
        public readonly UserStatus $status,
        public readonly ?string $country_code,
        public readonly ?string $mobile_no,
        public readonly ?string $email_verified_at,
        public readonly ?string $last_login_at,
        public readonly ?string $created_at,
        public readonly string $name,
        public readonly string $display_status,
        public readonly string $display_mobile_no,
        public readonly ?MediaData $profile_image,
    ) {}

    /**
     * Build a UserData instance from a User Eloquent model.
     */
    public static function fromModel(User $model): self
    {
        return new self(
            id: $model->id,
            first_name: $model->first_name,
            last_name: $model->last_name,
            username: $model->username,
            email: $model->email,
            status: $model->status,
            country_code: $model->country_code,
            mobile_no: $model->mobile_no,
            email_verified_at: $model->email_verified_at?->toDateTimeString(),
            last_login_at: $model->last_login_at?->toDateTimeString(),
            created_at: $model->created_at?->toDateTimeString(),
            name: $model->name,
            display_status: $model->display_status,
            display_mobile_no: $model->display_mobile_no,
            profile_image: Lazy::whenLoaded('profileImage', fn () => MediaData::fromModel($model->profileImage)),
        );
    }
}
