<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */

use MMWS\Interfaces\View;


class Module extends View
{
    /**
     * Creates an user
     */
    function version()
    {
        return $_ENV['APP_NAME'] . '^' . $_ENV['APP_VERSION'];
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
