<?php

declare(strict_types=1);

namespace App\Http\Resources\Media;

use App\Models\Media;
use App\Traits\ResourceFilterable;
use Dedoc\Scramble\Attributes\SchemaName;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Media $resource
 */
<<<<<<< HEAD
=======
#[SchemaName('Media')]
>>>>>>> origin/master
class Resource extends JsonResource
{
    use ResourceFilterable;

    /** @var class-string */
    protected $model = Media::class;

    /**
<<<<<<< HEAD
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
=======
     * @return array{
     *     id: int,
     *     disk: string,
     *     directory: string|null,
     *     filename: string,
     *     extension: string|null,
     *     mime_type: string|null,
     *     aggregate_type: string|null,
     *     size: int|null,
     *     created_at: int|null,
     *     updated_at: int|null,
     *     url: string,
     *     cdn_url: string|null
     * }
>>>>>>> origin/master
     */
    public function toArray($request): array
    {
        $data = $this->fields();
        $data['url'] = $this->resource->getUrl();
        $data['cdn_url'] = $this->getCdnUrl();

        return $data;
    }

    /**
     * Generate the CDN URL for the media if applicable.
     */
    private function getCdnUrl(): ?string
    {
        $cdnEnabled = config('media.cdn_enable');
        /** @var string|null $rawCdnUrl */
        $rawCdnUrl = config('media.cdn_url');
        $cdnUrl = rtrim($rawCdnUrl ?? '', '/');

        $mediaDisk = $this->resource->disk ?? null;
        if (! $cdnEnabled || $mediaDisk !== 's3' || empty($cdnUrl)) {
            return null;
        }

        $directory = trim($this->resource->directory ?? '', '/');
        $filename = $this->resource->filename ?? '';
        $extension = $this->resource->extension ?? '';

        if ($directory && $filename && $extension) {
            return sprintf('%s/%s/%s.%s', $cdnUrl, $directory, $filename, $extension);
        }

        return null;
    }
}
