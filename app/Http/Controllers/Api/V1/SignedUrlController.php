<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
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

    public function __invoke(SignedUrlRequest $request)
    {
        $signedUrlObj = $this->signedUrlService->create($request->all());

        return isset($signedUrlObj['errors']) ? $this->error($signedUrlObj) : $this->success($signedUrlObj, 200);
    }
}
