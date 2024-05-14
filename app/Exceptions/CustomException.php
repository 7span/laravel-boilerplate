<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;

class CustomException extends Exception
{
    use ApiResponser;

    public $message;

    public $code;

    public function __construct($message, $code = 400)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function report()
    {
        return '';
    }

    public function render()
    {
        $data['message'] = $this->message;
        $data['errors']['message'][] = $this->message;

        return $this->error($data, $this->code);
    }
}
