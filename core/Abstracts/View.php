<?php

namespace MMWS\Abstracts;

use MMWS\Factory\RequestExceptionFactory;
use MMWS\Handler\Request;

/**
 * Abstracts the main View methods
 * 
 * ----------
 * 
 * Example Usage:
 * ```php
 * use MMWS\Abstracts\View;
 * 
 * class View extends AbstractView
 * {
 *      method1() {
 *          $data = $this->data;
 *          // ...
 *      }  
 * }
 * ```
 * ----------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.6.1-alpha
 */
abstract class View
{
    /**
     * @var Request $request the request object
     */
    protected $request;

    /**
     * @var string $method the method name to call
     */
    protected $method = '';

    /**
     * @var array $data the request data params and body
     */
    protected $data = [];

    /**
     * @var array body request body
     */
    protected $body = [];

    /**
     * @var array params request params
     */
    protected $params = [];

    /**
     * @var array query request query
     */
    protected $query = [];


    function __construct(Request $request)
    {
        $this->request = $request;
        $this->body = $request->getBody();
        $this->params = $request->getParams();
        $this->query = $request->getQuery();
        $this->data = $request->data();
        $this->method = $request->getProcedure();
    }

    /**
     * Calls the procedure and initiates the 
     * request. If the called method does not
     * exist, it will throw an error.
     * 
     * @param MMWS\Handler\Request $request the request object
     * 
     * @return array the request result
     */
    function call()
    {
        if (method_exists($this, $this->method)) {
            $response = $this->{$this->method}();
            return $response;
        } else {
            throw RequestExceptionFactory::create(null, 404);
        }
    }
}
