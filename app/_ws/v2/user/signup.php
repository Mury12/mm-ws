<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */

use MMWS\Factory\RequestExceptionFactory;
use MMWS\Interfaces\View;


class Module extends View
{
    /**
     * Creates an user
     */
    function signup(): array
    {
        return [$this->data];
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
