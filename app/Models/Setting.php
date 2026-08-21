<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\HasUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'key',
    'value',
    'collection',
    'is_public',
    'updated_by',
])]
#[Hidden(['created_at', 'updated_at', 'deleted_at'])]
class Setting extends Model
{
    use BaseModel;
    use HasUserActions;
    use SoftDeletes;

    /** @var array<int, string> */
    protected array $exactFilters = [
        'key',
        'collection',
        'is_public',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
