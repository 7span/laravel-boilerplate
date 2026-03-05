<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property-read User|null $user
 * @property-read User|null $sender
 * @property int|null $read_at
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Notification extends Model
{
    use BaseModel;

    public $incrementing = false;

    public $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'sender' => [
            'model' => User::class,
        ],
        'user.media' => [
            'model' => Media::class,
        ],
        'sender.media' => [
            'model' => Media::class,
        ],
    ];

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

    protected $appends = ['user_with_type'];

    protected $scopedFilters = [
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopeIsRead($query, $isRead)
    {
        if ($isRead === 'true') {
            return $query->whereNotNull('read_at');
        }

        if ($isRead === 'false') {
            return $query->whereNull('read_at');
        }

        return $query;
    }

    protected function senderName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sender?->name,
        );
    }

    protected function buyerName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user?->name,
        );
    }

    protected function userWithType(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user?->name . ' (' . $this->type . ')',
        );
    }
}
