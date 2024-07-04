<?php

namespace App\Models;

use App\Traits\BaseModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use BaseModel, HasFactory;

    public $queryable = [
        'id',
    ];

    public $collections = [];

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

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $relationship = [];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getMediaUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->file_name);
    }
}
