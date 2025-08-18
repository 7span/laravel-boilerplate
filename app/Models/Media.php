<?php

declare(strict_types = 1);

namespace App\Models;

use App\Traits\BaseModel;
use Plank\Mediable\Media as MediableMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends MediableMedia
{
    use BaseModel, HasFactory;

    public $queryable = [
        'id',
    ];

    protected $fillable = [
        'disk',
        'directory',
        'filename',
        'extension',
        'mime_type',
        'aggregate_type',
        'size',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $relationship = [
        'updated_by_user' => [
            'model' => User::class,
        ],
        'media' => [
            'model' => Media::class,
        ],
    ];

    protected $exactFilters = [];

    protected $scopedFilters = [];

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
