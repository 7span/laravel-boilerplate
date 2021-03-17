<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOtp extends Model
{
    use SoftDeletes, BaseModel;

    protected $fillable = [
        'otp',
        'user_id',
        'otp_for',
        'used_at'
    ];

    protected $hidden = [
        'updated_at', 'deleted_at'
    ];

    public $queryable = [
        'id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $exactFilters = ['id', 'otp', 'otp_for', 'user_id'];

    protected $relationship = [
        'user' => [
            'model' => 'App\\Models\\User',
        ],
    ];
}
