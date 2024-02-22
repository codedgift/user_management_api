<?php

namespace App\Exceptions;
use Symfony\Component\HttpFoundation\Response;

use Exception;

class UserDeletedException extends Exception
{
    protected $message = 'Account with this email has been deleted.';
    protected $code = Response::HTTP_NOT_FOUND;

    // Optionally, you can customize the constructor if you need more flexibility
    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        // If a custom message is provided, use it, otherwise use the default
        $message = $message ?: $this->message;
        $code = $code ?: $this->code;

        parent::__construct($message, $code, $previous);
    }
}
