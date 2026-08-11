<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;

/** @property UserStatus|null $status */
#[Fillable([
    'first_name',
    'last_name',
    'username',
    'email',
    'email_verified_at',
    'password',
    'locale',
    'status',
    'country_code',
    'mobile_no',
    'last_login_at',
    'created_at',
])]
#[Hidden(['password', 'remember_token'])]
#[Appends(['name', 'display_status', 'display_mobile_no'])]
class User extends Authenticatable implements HasLocalePreference
{
    use BaseModel;
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Mediable;
    use Notifiable;
    use SoftDeletes;

    protected string $guard_name = 'api';

    public function preferredLocale(): string
    {
        return $this->locale ?? config('app.locale');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'timestamp',
            'password' => 'hashed',
            'last_login_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
            'status' => UserStatus::class,
        ];
    }

    /** @return Attribute<string, never> */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim("{$this->first_name} {$this->last_name}"),
        );
    }

    /** @return Attribute<string|null, never> */
    protected function displayStatus(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->status?->label(),
        );
    }

    /** @return Attribute<string, never> */
    protected function displayMobileNo(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim("{$this->country_code} {$this->mobile_no}"),
        );
    }
}
