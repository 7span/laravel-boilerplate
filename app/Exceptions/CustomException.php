<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;

class CustomException extends Exception
{
    use ApiResponser;

    public function __construct(public string $messageStr, public int $resCode = 400) {}

    public function report(): string
    {
        return '';
    }

    public function render(): \Illuminate\Http\JsonResponse
    {
        $data = [
            'message' => $this->messageStr,
            'errors' => [
                'message' => [$this->messageStr],
            ],
        ];

        return $this->error($data, $this->resCode);
    }
}
