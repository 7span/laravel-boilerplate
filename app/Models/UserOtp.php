<?php

namespace App\Models;

use App\Enums\UserOtpFor;
use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $otp
 * @property UserOtpFor $otp_for
 * @property int|null $verified_at
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read User|null $user
 */
#[Fillable([
    'user_id',
    'otp',
    'otp_for',
    'verified_at',
])]
#[Hidden(['updated_at', 'deleted_at'])]
class UserOtp extends Model
{
    use BaseModel;
    use SoftDeletes;

    /** @var array<int, string> */
    protected array $exactFilters = [
        'id',
        'otp',
        'otp_for',
        'user_id',
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
            'otp_for' => UserOtpFor::class,
            'verified_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
