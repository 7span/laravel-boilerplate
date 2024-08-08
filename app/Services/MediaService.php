<?php

namespace App\Services;

use App\Models\Media;
use App\Library\MediaHelper;

class MediaService
{
    public function __construct(private Media $mediaObj)
    {
        //
    }

    public function destroy($inputs)
    {
        $data = MediaHelper::destroyMedia($inputs);

        return $data;
    }
}
