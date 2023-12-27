<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\VerificationService;

class VerificationController extends Controller
{
    use ApiResponser;

    public function __construct(private VerificationService $verificationService)
    {
        //
    }

    /**
     * Verify Email
     */
    public function verify($userId, Request $request)
    {
        if (! $request->hasValidSignature()) {
            $data['errors']['message'] = __('message.invalidUrl');
        } else {
            $data = $this->verificationService->verify($userId, $request->all());
        }

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
