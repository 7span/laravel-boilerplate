<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    use BaseModel;

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

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'timestamp',
            'created_at' => 'timestamp',
        ];
    }

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopeIsRead(Builder $query, bool $isRead): Builder
    {
        return $isRead ? $query->whereNotNull('read_at') : $query->whereNull('read_at');
    }
}
