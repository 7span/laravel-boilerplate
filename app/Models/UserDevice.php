<?php

namespace App\Models;

use App\Enums\DeviceType;
use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property DeviceType|null $device_type */
#[Fillable([
    'user_id',
    'onesignal_player_id',
    'device_id',
    'device_type',
])]
#[Appends(['display_device_type'])]
class UserDevice extends Model
{
    use BaseModel;

    /** @var array<int, string> */
    protected array $exactFilters = [
        'user_id',
        'onesignal_player_id',
        'device_id',
        'device_type',
    ];

    /** @var array<string, array{model: class-string}> */
    protected array $relationship = [
        'user' => [
            'model' => User::class,
        ],
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'device_type' => DeviceType::class,
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    /** @return Attribute<string|null, never> */
    protected function displayDeviceType(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->device_type?->label(),
        );
    }
}
