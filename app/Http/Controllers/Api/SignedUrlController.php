<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use App\Services\SignedUrlService;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\SignedUrl\Request as SignedUrlRequest;

/**
 * @tags Signed URL
 */
#[Group('Signed URL', weight: 8)]
class SignedUrlController extends Controller
{
    use ApiResponser;

    private SignedUrlService $signedUrlService;

    public function __construct()
    {
        $this->signedUrlService = new SignedUrlService;
    }

    /**
     * Generate signed URL.
     *
     * Creates a time-limited signed URL that can be used for direct file uploads to storage.
     *
     * @response array{url: string, key: string, directory: string, filename: string}
     */
    public function __invoke(SignedUrlRequest $request)
    {
        $signedUrlObj = $this->signedUrlService->create($request->validated());

        return $this->success($signedUrlObj, 200);
    }
}
