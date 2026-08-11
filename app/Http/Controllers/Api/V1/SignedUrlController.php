<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use App\Services\SignedUrlService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\SignedUrl\GenerateSignedUrlRequest;

/**
 * @tags SignedUrl
 */
#[Group('SignedUrl', weight: 60)]
class SignedUrlController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly SignedUrlService $signedUrlService) {}

    /**
     * Generate URL.
     *
     * Returns a temporary URL the client PUTs the file to, plus the metadata to
     * send back on the endpoint that should own the file.
     *
     * @unauthenticated
     *
     * @response array{url: string, key: string, directory: string, filename: string}
     */
    public function __invoke(GenerateSignedUrlRequest $request): JsonResponse
    {
        $data = $this->signedUrlService->create($request->validated());

        return $this->success($data);
    }
}
