<?php

/**
 * This is the Composer Module.
 * A Module is a class that extends a a View, performing as 
 * a controller to a certain endpoint. Use this class to
 * perform calls to the actual controllers that execute
 * functions related to this procedures.
 * 
 * Description of this endpoint
 *
 *
 */

use MMWS\Factory\RequestExceptionFactory;
use MMWS\Interfaces\View;

class Module extends View
{
    /**
     * Method description
     */
    function exampleMethod()
    {
        if($this->data['body']['exampleProp']){
            // Logic
        } else {
            throw RequestExceptionFactory::create([
                'error' => 'Example error of wrong parameter',
                'fields' => ['exampleParam']
            ], 400);
        }
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
