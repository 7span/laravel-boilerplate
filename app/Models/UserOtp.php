<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserOtpFor;
use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $otp
 * @property \App\Enums\UserOtpFor $otp_for
 * @property string|null $verified_at
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class UserOtp extends Model
{
    use BaseModel;

    protected $fillable = [
        'otp',
        'user_id',
        'otp_for',
        'verified_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    /** @var array<int, string> */
    protected $exactFilters = [
        'id',
        'otp',
        'otp_for',
        'user_id',
    ];

    /** @var array<string, array<string, class-string>> */
    protected $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'media' => [
            'model' => Media::class,
        ],
    ];

    /**
     * Model's relationships
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\UserOtp>
     */
    public function user(): BelongsTo
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo<User, UserOtp> $rel @phpstan-ignore varTag.type */
        $rel = $this->belongsTo(User::class);

        return $rel;
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
            'otp_for' => UserOtpFor::class,
        ];
    }
}
