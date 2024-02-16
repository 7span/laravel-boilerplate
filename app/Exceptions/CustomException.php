<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public $message;

    public $code;

    public function __construct($message, $code = 401)
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
        return response()->json(['message' => $this->message], $this->code);
    }
}
