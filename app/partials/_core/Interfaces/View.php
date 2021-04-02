<?php

namespace MMWS\Interfaces;

use MMWS\Factory\RequestExceptionFactory;
use MMWS\Handler\Request;

/**
 * Abstracts the main View methods
 * 
 * ----------
 * 
 * Example Usage:
 * ```php
 * use MMWS\Interfaces\AbstractView;
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
class View
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

    function __construct(Request $request)
    {
        $this->request = $request;
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
    function call(): array
    {
        if (method_exists($this, $this->method)) {
            $response = $this->{$this->method}();
            return is_array($response) ? $response : [$response];
        } else {
            throw RequestExceptionFactory::create(null, 404);
        }
    }
}
