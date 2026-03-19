<?php

namespace App\Http\Resources\Media;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Media $resource
 */
#[SchemaName('Media')]
class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Media::class;

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * The unique identifier of the media file.
             */
            'id' => $this->id,
            /**
             * The storage disk where the file is stored (e.g. local, s3).
             */
            'disk' => $this->disk,
            /**
             * The directory path within the disk.
             */
            'directory' => $this->directory,
            /**
             * The filename without extension.
             */
            'filename' => $this->filename,
            /**
             * The file extension (e.g. jpg, png).
             */
            'extension' => $this->extension,
            /**
             * The MIME type of the file.
             */
            'mime_type' => $this->mime_type,
            /**
             * The aggregate type category (e.g. image, video, document).
             */
            'aggregate_type' => $this->aggregate_type,
            /**
             * The file size in bytes.
             */
            'size' => $this->size,
            /**
             * The public URL to access the file.
             *
             * @format uri
             */
            'url' => $this->getUrl(), // @phpstan-ignore-line
            /**
             * The CDN URL for the file, if CDN is enabled.
             *
             * @format uri
             */
            'cdn_url' => $this->getCdnUrl(),
        ];
    }

    /**
     * Generate the CDN URL for the media if applicable.
     */
    private function getCdnUrl(): ?string
    {
        $cdnEnabled = config('media.cdn_enable');
        $cdnUrl = rtrim(config('media.cdn_url'), '/');

        if (! $cdnEnabled || ($this->resource->disk ?? null) !== 's3' || empty($cdnUrl)) {
            return null;
        }

        $directory = trim($this->resource->directory ?? '', '/');
        $filename = $this->resource->filename ?? null;
        $extension = $this->resource->extension ?? null;

        if ($directory && $filename && $extension) {
            return sprintf('%s/%s/%s.%s', $cdnUrl, $directory, $filename, $extension);
        }

        return null;
    }
}
