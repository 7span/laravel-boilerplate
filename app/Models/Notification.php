<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use BaseModel, HasFactory;

    protected $table = 'notifications';
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
        'created_at'
    ];

    protected $relationship = [
        'user' => [
            'model' => User::class
        ],
        'sender' => [
            'model' => User::class
        ]
    ];

    protected $scopedFilters = [
        'is_read',
    ];

    public $queryable = [
        'id',
    ];

    protected $defaultSort = '-created_at';

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'timestamp',
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
