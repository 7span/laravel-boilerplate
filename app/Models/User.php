<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property UserStatus $status
 * @property string $password
 * @property string|null $country_code
 * @property string|null $mobile_no
 * @property int|null $email_verified_at
 * @property int|null $last_login_at
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property string $name
 * @property string $display_status
 * @property string $display_mobile_no
 */
class User extends Authenticatable
{
    use BaseModel, HasApiTokens, HasRoles, Mediable, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'status',
        'password',
        'country_code',
        'mobile_no',
        'email_verified_at',
        'last_login_at',
        'created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected string $guard_name = 'api';

    /** Accessors and Mutators */
    protected $appends = ['name', 'display_status', 'display_mobile_no'];

    /** @var array<string, array<string, class-string>> */
    protected array $relationship = [
        'user_devices' => [
            'model' => UserDevice::class,
        ],
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\UserDevice, \App\Models\User> */
    public function userDevices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        /** @var \Illuminate\Database\Eloquent\Relations\HasMany<UserDevice, User> $rel @phpstan-ignore varTag.type */
        $rel = $this->hasMany(UserDevice::class);

        return $rel;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\UserDevice, \App\Models\User> */
    public function user_devices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->userDevices();
    }

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
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }

    /** @return Attribute<string, never> */
    protected function displayStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status->label(),
        );
    }

    /** @return Attribute<string, never> */
    protected function displayMobileNo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->country_code . ' ' . $this->mobile_no,
        );
    }
}
