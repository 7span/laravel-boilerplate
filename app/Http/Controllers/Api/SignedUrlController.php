<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use OpenApi\Attributes as OA;
use App\Services\SignedUrlService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignedUrl\Request as SignedUrlRequest;

class SignedUrlController extends Controller
{
    use ApiResponser;

    private SignedUrlService $signedUrlService;

    public function __construct()
    {
        $this->signedUrlService = new SignedUrlService;
    }

    #[OA\Post(
        path: '/api/generate-signed-url',
        operationId: 'generate-signed-url',
        tags: ['SignedUrl'],
        summary: 'Generate signed url',
    )]
    public function __invoke(SignedUrlRequest $request)
    {
        $signedUrlObj = $this->signedUrlService->create($request->validated());

        return $this->success($signedUrlObj, 200);
    }
}
