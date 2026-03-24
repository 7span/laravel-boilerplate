<?php

<<<<<<< HEAD:app/Http/Controllers/Api/SignedUrlController.php
declare(strict_types=1);

namespace App\Http\Controllers\Api;
=======
namespace App\Http\Controllers\Api\V1;
>>>>>>> origin/master:app/Http/Controllers/Api/V1/SignedUrlController.php

use App\Traits\ApiResponser;
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

    private SignedUrlService $signedUrlService;

    public function __construct()
    {
        $this->signedUrlService = new SignedUrlService;
    }

<<<<<<< HEAD:app/Http/Controllers/Api/SignedUrlController.php
    #[OA\Post(
        path: '/api/generate-signed-url',
        operationId: 'generate-signed-url',
        tags: ['SignedUrl'],
        summary: 'Generate signed url',
    )]
    public function __invoke(SignedUrlRequest $request): \Illuminate\Http\JsonResponse
=======
    /**
     * Generate URL.
     *
     * @unauthenticated
     *
     * @response array{url: string, key: string, directory: string, filename: string}
     */
    public function __invoke(SignedUrlRequest $request)
>>>>>>> origin/master:app/Http/Controllers/Api/V1/SignedUrlController.php
    {
        $signedUrlObj = $this->signedUrlService->create($request->validated());

        return $this->success($signedUrlObj, 200);
    }
}
