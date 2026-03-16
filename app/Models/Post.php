<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use BaseModel;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'ulid',
        'category_id',
        'post_name',
        'post_date',
    ];

    protected $casts = [
        'post_date' => 'datetime',
        'created_at' => 'timestamp',
    ];

    protected $defaultSort = '-created_at';

    protected $hidden = ['id', 'category_id'];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if (empty($post->ulid)) {
                $post->ulid = (string) Str::ulid();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

