<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterSetting extends Model
{
    use HasFactory, BaseModel, SoftDeletes;

    public $fillable = [
        'key',
        'value',
        'collection'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public $queryable = [
        'id',
    ];

    protected $relationship = [];
}
