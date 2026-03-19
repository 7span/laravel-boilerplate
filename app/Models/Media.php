<?php

namespace App\Models;

use App\Traits\BaseModel;
use App\Traits\HasUserActions;
use Plank\Mediable\Media as MediableMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $appends = [
        'cdn_url',
    ];

    protected $relationship = [
        'media' => [
            'model' => Media::class,
        ],
    ];

    protected function cdnUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $cdnEnabled = config('media.cdn_enable');
                $cdnUrl = rtrim(config('media.cdn_url'), '/');

                if (! $cdnEnabled || ($this->disk ?? null) !== 's3' || empty($cdnUrl)) {
                    return;
                }

                $directory = trim($this->directory ?? '', '/');
                $filename = $this->filename ?? null;
                $extension = $this->extension ?? null;

                if ($directory && $filename && $extension) {
                    return sprintf('%s/%s/%s.%s', $cdnUrl, $directory, $filename, $extension);
                }
            }
        );
    }
}
