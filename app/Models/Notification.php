<?php

namespace App\Models;

use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use BaseModel, Mediable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'sent_by',
        'title',
        'description',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $defaultSort = '-created_at';

    protected $scopedFilters = [
        'is_read',
    ];

    protected $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'sender' => [
            'model' => User::class,
        ],
        'media' => [
            'model' => Media::class,
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
