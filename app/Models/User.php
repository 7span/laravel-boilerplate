<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use BaseModel, HasApiTokens, HasFactory, HasRoles, Mediable, Notifiable, SoftDeletes;

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

    protected $guard_name = 'api';

    protected $relationship = [];

    /** Accessors and Mutators */
    protected $appends = ['name', 'display_status', 'display_mobile_no'];

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


    public $queryable = [
        'id',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->first_name . ' ' . $this->last_name,
        );
    }

    protected function displayStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status->label(), // @phpstan-ignore-line
        );
    }

    protected function displayMobileNo(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->country_code . ' ' . $this->mobile_no,
        );
    }
}