<?php

namespace App\Http\Controllers\Api;

use App\Models\Media;
use App\Traits\ApiResponser;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;

/**
 * @tags Media
 */
#[Group('Media', weight: 7)]
class MediaController extends Controller
{
    use ApiResponser;

    public function __construct(private MediaService $mediaService) {}

    /**
     * Delete media file.
     *
     * Permanently deletes the specified media file belonging to the authenticated user.
     *
     * @response array{message: string}
     */
    public function destroy(Media $media): JsonResponse
    {
        $data = $this->mediaService->destroy($media);

        return $this->success($data);
    }
}
