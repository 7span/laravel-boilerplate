<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory, BaseModel;

    protected $fillable = [
        'disk',
        'directory',
        'file_name',
        'original_file_name',
        'extension',
        'mime_type',
        'aggregate_type',
        'size',
        'mediable_type',
        'mediable_id',
        'tag',
        'created_at',
        'updated_at',
    ];

    public $queryable = [
        'id',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    public $collections = [];

    protected $relationship = [];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
