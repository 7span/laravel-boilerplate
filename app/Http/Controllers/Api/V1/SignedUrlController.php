<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Services\SignedUrlService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\SignedUrl\Request as SignedUrlRequest;

/**
 * @tags SignedUrl
 */
#[Group('SignedUrl', weight: 60)]
class SignedUrlController extends Controller
{
    use ApiResponser;

    public function __construct(private SignedUrlService $signedUrlService) {}

    /**
     * Generate URL.
     *
     * @unauthenticated
     *
     * @response array{url: string, key: string, directory: string, filename: string}
     */
    public function __invoke(SignedUrlRequest $request): JsonResponse
    {
        $signedUrlObj = $this->signedUrlService->create($request->validated());

        return $this->success($signedUrlObj, 200);
    }
}
