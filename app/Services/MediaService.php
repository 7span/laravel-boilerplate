<?php

namespace App\Services;

use App\Models\Media;
use App\Libraries\MediaHelper;
use App\Exceptions\CustomException;

class MediaService
{
    private $mediaObj;

    public function __construct()
    {
        $this->mediaObj = new Media();
    }

    public function destroy(int|string $id)
    {
        $media = $this->mediaObj->find($id);

        if (! $media) {
            throw new CustomException(__('message.mediaNotFound'));
        } else {
            $data = MediaHelper::destroyMedia($media);
        }

        return $data;
    }
}
