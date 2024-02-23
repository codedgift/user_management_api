<?php

namespace App\Exceptions;
use Symfony\Component\HttpFoundation\Response;

use Exception;

class InvalidCredentialsException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Invalid credentials provided';

    /**
     * @var int
     */
    protected $code = Response::HTTP_NOT_FOUND;


    /**
     * @param $message
     * @param $code
     * @param Exception|null $previous
     */
    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        // If a custom message is provided, use it, otherwise use the default
        $message = $message ?: $this->message;
        $code = $code ?: $this->code;

        parent::__construct($message, $code, $previous);
    }
}
