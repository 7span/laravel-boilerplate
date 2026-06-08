<?php

declare(strict_types=1);

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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /** @var array<string, array<string, class-string>> */
    protected $relationship = [
        'media' => [
            'model' => Media::class,
        ],
    ];
}
