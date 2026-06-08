<?php

declare(strict_types=1);

namespace App\Services;

use App\Libraries\MediaHelper;

class MediaService
{
    public function __construct() {}

    /** @return array<string, string> */
    public function destroy(\App\Models\Media $media): array
    {
        return MediaHelper::destroyMedia($media);
    }
}
