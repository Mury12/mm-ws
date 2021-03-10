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
    function returnRequestData(): array
    {

        if (sizeof($this->data['body']) || sizeof($this->data['params'])) {
            return $this->data;
        } else {
            throw RequestExceptionFactory::create([
                'error' => 'Request body and params are empty!',
                'fields' => ['field1', 'field2']
            ], 400);
        }
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
