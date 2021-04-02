<?php

namespace MMWS\Handler;

use Exception;

class RequestException extends Exception
{
    protected $code = 500;
    protected $message = '';

    /**
     * Sets the message to send in a request.
     * If null, it will set to empty
     */
    function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Sets the code to send in a request.
     * If null, it will set to 500.
     */
    function setCode(int $code)
    {
        $this->code = $code;
    }
}
