<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\HasUserActions;
use Plank\Mediable\Media as MediableMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;

/**
 * @property int $id
 * @property string $disk
 * @property string $directory
 * @property string $filename
 * @property string $extension
 * @property string $mime_type
 * @property string $aggregate_type
 * @property int $size
 * @property int|null $created_at
 * @property int|null $updated_at
 */
#[Fillable([
    'disk',
    'directory',
    'filename',
    'extension',
    'mime_type',
    'aggregate_type',
    'size',
    'created_at',
    'updated_at',
])]
class Media extends MediableMedia
{
    use BaseModel;
    use HasUserActions;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ];
    }
}
