<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\MediaService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\DeleteMedia;

class MediaController extends Controller
{
    use ApiResponser;

    public function __construct(private MediaService $mediaService)
    {
        //
    }

    public function destroy(DeleteMedia $request)
    {
        $data = $this->mediaService->destroy($request->validated());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
