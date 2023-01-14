<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */

use MMWS\Abstracts\View;
use MMWS\Model\UniqueId;

class Module extends View
{
    /**
     * Creates an user
     */
    function getUniqueId(): UniqueId
    {
        $params = $this->data['params'];
        return new UniqueId($params['len'] ?? 6, $params['hash'] ?? 'sha256');
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
