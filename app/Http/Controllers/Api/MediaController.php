<?php

namespace App\Http\Controllers\Api;

use App\Models\Media;
use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    use ApiResponser;

    public function __construct(private MediaService $mediaService) {}

    #[OA\Delete(
        path: '/api/media/{media}',
        operationId: 'adminDeleteMedia',
        tags: ['Media'],
        summary: 'Mobile > Delete media file',
        security: [['bearerAuth' => []]]
    )]
    public function destroy(Media $media): JsonResponse
    {
        $data = $this->mediaService->destroy($media);

        return $this->success($data);
    }
}
