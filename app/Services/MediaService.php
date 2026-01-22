<?php

namespace App\Services;

use App\Libraries\MediaHelper;

class MediaService
{
    public function __construct() {}

    public function destroy(object $media): array
    {
        return MediaHelper::destroyMedia($media);
    }
}
