<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\Contracts\OAuthenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;

/**
 * @property UserStatus $status
 */
class User extends Authenticatable implements HasLocalePreference, OAuthenticatable
{
    use BaseModel, HasApiTokens, HasRoles, Mediable, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'locale',
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

    protected $guard_name = 'api';

    /** Accessors and Mutators */
    protected $appends = ['name', 'display_status', 'display_mobile_no'];

    protected $relationship = [
        'user_device' => [
            'model' => UserDevice::class,
        ],
    ];

    public function userDevice()
    {
        return $this->hasOne(UserDevice::class);
    }

    public function preferredLocale(): string
    {
        return $this->locale ?? config('app.locale');
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

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }

    protected function displayStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status->label(),
        );
    }

    protected function displayMobileNo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->country_code . ' ' . $this->mobile_no,
        );
    }
}
