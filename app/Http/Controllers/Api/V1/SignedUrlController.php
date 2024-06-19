<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Services\SignedUrlService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignedUrl\Request as SignedUrlRequest;

class SignedUrlController extends Controller
{
    use ApiResponser;

    private $signedUrlService;

    public function __construct()
    {
        $this->signedUrlService = new SignedUrlService;
    }

    #[OA\Post(
        path: '/api/v1/generate-signed-url',
        operationId: 'generate-signed-url',
        tags: ['SignedUrl'],
        summary: 'Generate signed url',
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
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['filename', 'directory', 'mime_type'],
                properties: [
                    new OA\Property(
                        property: 'filename',
                        type: 'string',
                        format: 'filename',
                        example: 'Test'
                    ),
                    new OA\Property(
                        property: 'directory',
                        type: 'string',
                        format: 'directory',
                        example: 'User'
                    ),
                    new OA\Property(
                        property: 'mime_type',
                        type: 'string',
                        format: 'mime_type',
                        example: 'test'
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function __invoke(SignedUrlRequest $request)
    {
        $signedUrlObj = $this->signedUrlService->create($request->all());

        return isset($signedUrlObj['errors']) ? $this->error($signedUrlObj) : $this->success($signedUrlObj, 200);
    }
}
