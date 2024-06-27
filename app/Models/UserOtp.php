<?php

namespace App\Models;

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
    ];

    protected $cast = [
        'created_at' => 'timestamp',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Model's relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
