<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;

class CustomException extends Exception
{
    use ApiResponser;

    public function __construct(public string $messageStr, public int $resCode = 400) {}

    public function report()
    {
        return '';
    }

    public function render()
    {
        $data['message'] = $this->messageStr;
        $data['errors']['message'][] = $this->messageStr;

        return $this->error($data, $this->resCode);
    }
}
