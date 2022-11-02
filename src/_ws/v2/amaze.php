<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */

use MMWS\Factory\RequestExceptionFactory;
use MMWS\Abstracts\View;


class Module extends View
{
    /**
     * Creates an user
     */
    function me(): array
    {
        return ['I am running :D'];
    }

    function errors(): array
    {
        $code = $this->data['params']['code'];
        if ($code) {
            throw RequestExceptionFactory::create('', $code);
        } else {
            throw RequestExceptionFactory::field(['code']);
        }
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
