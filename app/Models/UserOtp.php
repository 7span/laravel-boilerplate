<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\UserOtpFor;
use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserOtp extends Model
{
    use BaseModel, HasFactory;

    public $queryable = [
        'id',
    ];

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

    protected $exactFilters = [
        'id',
        'otp',
        'otp_for',
        'user_id',
    ];

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
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
