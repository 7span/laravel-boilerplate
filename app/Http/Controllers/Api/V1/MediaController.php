<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Media;
use App\Traits\ApiResponser;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;

/**
 * @tags Media
 */
#[Group('Media', weight: 50)]
class MediaController extends Controller
{
    use ApiResponser;

    public function __construct(private MediaService $mediaService) {}

    /**
     * Delete.
     *
     * @response array{message: string}
     */
    public function destroy(Media $media): JsonResponse
    {
        $data = $this->mediaService->destroy($media);

        return $this->success($data);
    }
}
