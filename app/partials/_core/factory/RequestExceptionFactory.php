<?php

namespace MMWS\Factory;

use Exception;
use MMWS\Handler\RequestException;

class RequestExceptionFactory
{

    /**
     * Instantiates a RequestException with a message and a code.
     * If none of this is set, then it will use the Exception defaults.
     * @param mixed $message a message or array 
     * @param int $code the http code
     * 
     */
    static function create($message, int $code): RequestException
    {
        return self::createMessage($message, $code);
    }

    /** 
     * Creates a field misfilled exception
     * @param string[] $fields field names
     * @param int $code erro code (default 400 - bad request)
     * @param string $message the message to display. A default message is set.
     * 
     */
    static function field(array $fields, int $code = 400, string $message = null): RequestException
    {
        $error = [
            'error' => $message ?? 'Alguns campos não foram preenchidos corretamente',
            'fields' => $fields,
        ];
        return self::createMessage($error, $code);
    }

    /**
     * Creates a Too May Requests exception
     * @param int $tll time to be allowed in seconds
     * @param string $message the message to display. A default message is set.
     */
    static function tmr429(int $ttl = null, string $message = null): RequestException
    {
        $error = [
            'error' => $message ?? 'Muitas requisições em um período curto de tempo. Por favor, tente mais tarde.',
            'ttl' => $ttl ?? null
        ];
        return self::createMessage($error, 429);
    }

    /**
     * Creates the throwable message
     * 
     * @param mixed $message the message
     * @param int $code the code to be thrown
     */
    static private function createMessage($message, int $code): RequestException
    {
        $error = array();
        if (!is_array($message)) {
            $error = [
                'error' => $message
            ];
        } else $error = $message;
            $ex = new RequestException();
        if ($code)
            $ex->setCode($code);
        if ($message)
            $ex->setMessage($error);

        return $ex;
    }
}
