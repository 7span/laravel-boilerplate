<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
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
])]
class Notification extends Model
{
    use BaseModel;

    public $incrementing = false;

    protected $keyType = 'string';

    protected string $defaultSort = '-created_at';

    /** @var array<int, string> */
    protected array $scopedFilters = [
        'is_read',
    ];

    /** @var array<int, string> */
    protected array $exactFilters = [
        'type',
        'notifiable_type',
        'notifiable_id',
    ];

    /** @var array<string, array{model: class-string}> */
    protected array $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'sender' => [
            'model' => User::class,
        ],
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<User, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /** @return MorphTo<Model, $this> */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * GET /api/v1/notifications?filter[is_read]=0
     *
     * @param  Builder<$this>  $query
     */
    #[Scope]
    protected function isRead(Builder $query, mixed $isRead = true): void
    {
        $query->whereNull('read_at', not: filter_var($isRead, FILTER_VALIDATE_BOOLEAN));
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }
}
