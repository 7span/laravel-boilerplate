<?php

namespace App\Http\Controllers\Api\V1;

use App\Library\MediaHelper;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\DeleteMedia;

class MediaController extends Controller
{
    use ApiResponser;

    public function destroy(DeleteMedia $request)
    {
        $data = MediaHelper::destroyMedia($request->validated());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
