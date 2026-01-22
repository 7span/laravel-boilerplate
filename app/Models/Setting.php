<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\HasUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use BaseModel, HasUserActions, SoftDeletes;

    public $fillable = [
        'key',
        'value',
        'collection',
        'is_public', // If key is false, visible only for authenticated user. If true, visible for every user.
        'updated_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];
    }
}
