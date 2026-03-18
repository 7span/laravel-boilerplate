<?php

namespace App\Http\Resources\Media;

use App\Models\Media;
use App\Traits\ResourceFilterable;
use Illuminate\Support\Facades\Storage;
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
    public function toArray($request): array
    {
        $data = $this->fields();
        $data['is_private'] = $this->isPrivate();
        $data['url'] = $this->resolveUrl(); // @phpstan-ignore-line
        $data['cdn_url'] = $this->isPrivate() ? null : $this->getCdnUrl();

        return $data;
    }

    private function resolveUrl(): ?string
    {
        if ($this->isPrivate()) {
            return $this->getTemporaryUrl() ?? $this->getUrl();
        }

        return $this->getCdnUrl() ?? $this->getUrl(); // fallback if CDN disabled
    }

    private function isPrivate(): bool
    {
        $directory = trim($this->resource->directory ?? '', '/');

        return in_array(
            $directory,
            config('media.private_directories', []),
            true
        );
    }

    private function getTemporaryUrl(): ?string
    {
        if (($this->resource->disk ?? null) !== 's3') {
            return null;
        }

        $directory = trim($this->resource->directory ?? '', '/');
        $filename = $this->resource->filename ?? null;
        $extension = $this->resource->extension ?? null;

        if (! $directory || ! $filename || ! $extension) {
            return null;
        }

        $key = "{$directory}/{$filename}.{$extension}";

        return Storage::disk('s3')->temporaryUrl(
            $key,
            now()->addMinutes((int) config('media.temporary_url_expire_minutes', 5)),
            [
                'ResponseContentType' => $this->resource->mime_type,
            ]
        );
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
