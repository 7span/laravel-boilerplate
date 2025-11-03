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
        path: '/api/v1/media/{mediaId}',
        operationId: 'adminDeleteMedia',
        tags: ['Media'],
        summary: 'Mobile > Delete media file',
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
            new OA\Parameter(
                name: 'mediaId',
                in: 'path',
            ),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Success.'),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
        security: [['bearerAuth' => []]]
    )]
    public function destroy($id)
    {
        $data = $this->mediaService->destroy($id);

        return $this->success($data);
    }
}
