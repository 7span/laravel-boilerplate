<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\HasUserActions;
use Plank\Mediable\Media as MediableMedia;

/**
 * @property int|null $created_at
 * @property int|null $updated_at
 */
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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $relationship = [
        'media' => [
            'model' => Media::class,
        ],
    ];
}
