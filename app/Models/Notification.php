<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'data' => 'array',
        'read_at' => 'timestamp',
        'created_at' => 'timestamp',
    ];

    /** @var string */
    protected $defaultSort = '-created_at';

    /** @var array<int, string> */
    protected $scopedFilters = [
        'is_read',
    ];

    /** @var array<string, array<string, class-string>> */
    protected $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'sender' => [
            'model' => User::class,
        ],
    ];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Notification> */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Notification> $rel @phpstan-ignore varTag.type */
        $rel = $this->belongsTo(User::class, 'user_id');

        return $rel;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\Notification> */
    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo<User, Notification> $rel @phpstan-ignore varTag.type */
        $rel = $this->belongsTo(User::class, 'sent_by');

        return $rel;
    }
}
