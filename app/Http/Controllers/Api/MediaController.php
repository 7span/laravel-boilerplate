<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Services\MediaService;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    use ApiResponser;

    public function __construct(private MediaService $mediaService) {}

    #[OA\Delete(
        path: '/api/media/{mediaId}',
        operationId: 'adminDeleteMedia',
        tags: ['Media'],
        summary: 'Mobile > Delete media file',
        security: [['bearerAuth' => []]]
    )]
    public function destroy($id)
    {
        $data = $this->mediaService->destroy($id);

        return $this->success($data);
    }
}
