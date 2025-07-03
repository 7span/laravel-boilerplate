<?php

namespace App\Http\Resources\Media;

use App\Models\Media;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ResourceFilterable;

    protected $model = Media::class;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = $this->fields();
        $data['url'] = $this->getUrl(); // @phpstan-ignore-line
        $data['cdn_url'] = $this->getCdnUrl();

        return $data;
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
