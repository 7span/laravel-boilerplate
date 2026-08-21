<?php

namespace App\Services;

use App\Models\Media;
use App\Libraries\MediaHelper;

class MediaService
{
    /**
     * @return array{message: string}
     */
    public function destroy(Media $media): array
    {
        return MediaHelper::destroyMedia($media);
    }
}
