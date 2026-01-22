<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\HasUserActions;
use Plank\Mediable\Media as MediableMedia;

class Media extends MediableMedia
{
    use BaseModel, HasUserActions;

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

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
