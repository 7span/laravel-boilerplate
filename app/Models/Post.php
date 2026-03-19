<?php

namespace App\Models;

use App\Traits\BaseModel;
use Plank\Mediable\Mediable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    use BaseModel, Mediable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'status',
        'is_published',
        'published_at',
    ];

    protected $relationship = [
        'user' => [
            'model' => User::class,
        ],
    ];

    protected $append = ['name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => 'harshil',
        );
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'timestamp',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
