<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class CustomException extends Exception
{
    use ApiResponser;

    public function __construct(public string $messageStr, public int $resCode = 400)
    {
        parent::__construct($messageStr, $resCode);
    }

    public function report(): bool
    {
        return true;
    }

    public function render(): JsonResponse
    {
        $data['message'] = $this->messageStr;
        $data['errors']['message'][] = $this->messageStr;

        return $this->error($data, $this->resCode);
    }
}
