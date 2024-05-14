<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    use BaseModel, HasFactory;

    protected $fillable = [
        'otp',
        'user_id',
        'otp_for',
        'verified_at',
    ];

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];

    public $queryable = [
        'id',
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

    protected $cast = [
        'created_at' => 'timestamp',
    ];

    protected $dates = [
        'created_at',
    ];
}
