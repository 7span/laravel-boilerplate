<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Laravel\Passport\HasApiTokens;
use Database\Factories\UserFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\Contracts\OAuthenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
class User extends Authenticatable implements HasLocalePreference, OAuthenticatable
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

    /**
     * Media tags of this model, documented as `?media=` in the API docs.
     *
     * @var array<int, string>
     */
    protected array $mediaTags = [
        'profile',
    ];

    /** @var array<string, array{model: class-string}> */
    protected array $relationship = [
        'user_devices' => [
            'model' => UserDevice::class,
        ],
    ];

    public function preferredLocale(): string
    {
        return $this->locale ?? config('app.locale');
    }

    /**
     * The application stores notifications in its own table, so the relation from
     * `Notifiable` is replaced by one pointing at App\Models\Notification.
     *
     * @return HasMany<Notification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->latest('created_at');
    }

    /** @return HasMany<UserDevice, $this> */
    public function userDevices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * Resolve the OneSignal player ids the push notifications are delivered to.
     *
     * @return array<int, string>
     */
    public function routeNotificationForOneSignal(): array
    {
        return $this->userDevices()->pluck('onesignal_player_id')->all();
    }

    /**
     * The OneSignal app the pushes for this user go through. App keys are role
     * names, so the first one listed that the user holds wins.
     */
    public function routeNotificationForOneSignalApp(): ?string
    {
        return collect(array_keys(config('services.onesignal.apps')))
            ->intersect($this->getRoleNames())
            ->first();
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
