<?php

namespace App\Services;

use App\Models\Media;
use App\Libraries\MediaHelper;

class MediaService
{
    private $mediaObj;

    public function __construct()
    {
        $this->mediaObj = new Media;
    }

    public function destroy(int|string $id)
    {
        $media = $this->mediaObj->findOrFail($id);

        return MediaHelper::destroyMedia($media);
    }
}
