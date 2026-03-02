<?php

namespace App\Data;

use App\Models\User;
use App\Enums\UserStatus;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use App\Data\Concerns\InteractsWithRequestedMedia;

class UserData extends Data
{
    use InteractsWithRequestedMedia;

    public function __construct(
        public int $id,
        public ?string $first_name,
        public ?string $last_name,
        public ?string $username,
        public ?string $email,
        public ?UserStatus $status,
        public ?string $country_code,
        public ?string $mobile_no,
        public ?int $email_verified_at,
        public ?int $last_login_at,
        public ?int $created_at,
        public string $name,
        public string $display_status,
        public string $display_mobile_no,
        public MediaData|Optional|null $profile,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            first_name: $user->first_name,
            last_name: $user->last_name,
            username: $user->username,
            email: $user->email,
            status: $user->status,
            country_code: $user->country_code,
            mobile_no: $user->mobile_no,
            email_verified_at: $user->email_verified_at,
            last_login_at: $user->last_login_at,
            created_at: $user->created_at,
            name: (string) $user->name,
            display_status: (string) $user->display_status,
            display_mobile_no: (string) $user->display_mobile_no,
            profile: self::firstMediaDataOrOptional($user, 'profile'),
        );
    }
}
