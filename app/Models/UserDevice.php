<?php

declare(strict_types = 1);

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use BaseModel;

    protected $fillable = [
        'user_id',
        'onesignal_player_id',
        'device_id',
        'device_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts()
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }
}
