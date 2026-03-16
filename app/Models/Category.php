<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use BaseModel;

    public $incrementing = false;

    protected $keyType = 'string';

    public function getRouteKeyName()
    {
        return 'ulid';
    }

    protected $fillable = [
        'id',
        'ulid',
        'name',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
    ];

    protected $defaultSort = '-created_at';

    protected $hidden = ['id'];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->ulid)) {
                $category->ulid = (string) Str::ulid();
            }
        });
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

