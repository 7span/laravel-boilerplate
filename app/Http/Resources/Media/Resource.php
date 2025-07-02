<?php

namespace App\Http\Resources\Media;

use App\Models\Media;
use Sevenspan\Bunny\Bunny;
use App\Traits\ResourceFilterable;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UpdatedByResource;

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
        $data['url'] = $this->getUrl();
        $data['cdn_url'] = $this->getCdnUrl();

        return $data;
    }

    /**
     * Generate the CDN URL for the media if applicable.
     *
     * @return string|null
     */
    private function getCdnUrl()
    {
        $cdnEnabled = config('media.cdn_enable');
        $cdnUrl = rtrim(config('media.cdn_url'), '/');

        if (!$cdnEnabled || $this->disk !== 's3' || empty($cdnUrl)) {
            return null;
        }

        $directory = trim($this->directory, '/');
        $filename = $this->filename;
        $extension = $this->extension;

        return sprintf('%s/%s/%s.%s', $cdnUrl, $directory, $filename, $extension);
    }
}
