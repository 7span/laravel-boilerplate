<?php

namespace App\Http\Resources\Media;

use App\Models\Media;
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
     */
    public function toArray($request)
    {
        $data = $this->fields();
        $data['url'] = $this->getUrl(); // @phpstan-ignore-line

        // $data['cdn_url'] = $this->getCdnUrl();
        return $data;
    }

    /**
     * Generate the CDN URL for the media if applicable.
     */
    // private function getCdnUrl(): ?string
    // {
    //     $cdnEnabled = config('media.cdn_enable');
    //     $cdnUrl = rtrim(config('media.cdn_url'), '/');

    //     if (! $cdnEnabled || ($this->resource->disk ?? null) !== 's3' || empty($cdnUrl)) {
    //         return null;
    //     }

    //     $directory = trim($this->resource->directory ?? '', '/');
    //     $filename = $this->resource->filename ?? null;
    //     $extension = $this->resource->extension ?? null;

    //     if ($directory && $filename && $extension) {
    //         return sprintf('%s/%s/%s.%s', $cdnUrl, $directory, $filename, $extension);
    //     }

    //     return null;
    // }
}
