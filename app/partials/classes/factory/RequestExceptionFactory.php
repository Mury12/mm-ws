<?php

namespace MMWS\Factory;

use MMWS\Handler\RequestException;

class RequestExceptionFactory
{
    /**
     * Instantiates a RequestException with a message and a code.
     * If none of this is set, then it will use the Exception defaults.
     * @param mixed $message a message or array 
     * @param int $code the http code
     * 
     * @return MMWS\Handler\RequestException
     */
    static function create($message, int $code)
    {
        $ex = new RequestException();
        if ($code)
            $ex->setCode($code);
        if ($message)
            $ex->setMessage($message);

        return $ex;
    }
}
