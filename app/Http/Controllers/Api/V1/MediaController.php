<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Media;
use App\Library\MediaHelper;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    use ApiResponser;

    public function destroy($id)
    {
        $media = Media::find($id);

        if (! $media) {
            $data['errors']['media_id'][] = __('message.mediaNotFound');
            $data['message'] = __('message.mediaNotFound');
        } else {
            $data = MediaHelper::destoryMedia($media);
        }

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
