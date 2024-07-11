<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterSetting extends Model
{
    use BaseModel, HasFactory, SoftDeletes;

    public $fillable = [
        'key',
        'value',
        'collection',
        'is_public', // If key is false, visible only for authenticated user. If true, visible for every user.
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
