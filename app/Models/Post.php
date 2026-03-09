<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use BaseModel, HasFactory;

    public $relationship = [
        'user' => [
            'model' => User::class,
        ],
        'user.media' => [
            'model' => Media::class,
        ],
    ];

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'published_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }

    /**
     * Accessors
     */
    protected function displayTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => ucwords($this->title),
        );
    }

    /**
     * Mutators
     */
    protected function publishedAt(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null,
        );
    }
}
