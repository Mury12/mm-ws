<?php

namespace MMWS\Handler;

use MMWS\Interfaces\Middleware;

/**
 * Handles the HTTP requests
 * 
 * This handler is not done yet. DO NOT USE IT
 * @ignore
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.9.1-alpha
 */
class Request
{
    /**
     * @var Array<Array> $request contains the endpoint [request_method] => ['page', 'procedure'] for each endpoint set to the same file.
     */
    private $request = [];

    private $procedure = '';
    private $body = [];
    private $params = [];
    private $query = [];
    private $method = null;
    private $opts = [];

    function __construct()
    {
    }

    /**
     * Adds a request method to an endpoint
     * @param String $method GET|POST|DELETE|PATCH|PUT
     * @param String $page the actual file to the endpoint
     * @param String $procedure the method to be called in $file
     */
    public function add(String $method, String $page, String $procedure, array $opts = [])
    {
        $this->request[strtoupper($method)] = [
            'page' => $this->setFilePath($page),
            'procedure' => $procedure,
            'opts' => $opts
        ];

        return $this;
    }

    /**
     * Gets the endpoint configuration method, request and file
     * @return bool|Array the configurations or false if not exists.
     */
    public function get(String $method)
    {
        if (array_key_exists(strtoupper($method), $this->request)) {
            return $this->request[strtoupper($method)];
        }
        return false;
    }

    /**
     * Sets the Body of the request
     */
    function setBody(?array $body)
    {
        if ($body && sizeof($body)) {
            $this->body = $body;
        } else {
            throw new RequestException("Body cannot be empty for POST|PATCH|PUT requests.", 400);
        }
    }

    /**
     * Returns the Body of the request
     * @return mixed[]
     */
    function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the Params of the request
     */
    function setParams(array $params)
    {
        $this->params = $params;
    }


    /**
     * Returns the Params of the request
     * @return string[]
     */
    function getParams()
    {
        return $this->params;
    }

    /**
     * Sets the URL query of the request
     */
    function setQuery(array $query)
    {
        $this->query = CaseHandler::convert($query, 1);
    }

    /**
     * Returns the Params of the request
     * @return string[]
     */
    function getQuery()
    {
        return $this->query;
    }
    /**
     * Sets the Procedure of the request
     */
    function setProcedure($procedure)
    {
        $this->procedure = $procedure;
    }

    /**
     * Returns the Procedure of the request
     * @return string
     */
    function getProcedure()
    {
        return $this->procedure;
    }

    /**
     * Sets the current request method
     */
    function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Returns the method of the request
     * @return string
     */
    function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets route options to a specific method
     */
    function setOpts(array $opts)
    {
        $this->opts = $opts;
    }

    /**
     * Gets the current request method options
     */
    function getOpts()
    {
        return $this->opts;
    }

    /**
     * Gets the current middlewares for this method
     * 
     * @return Middleware[]
     */
    function getMiddlewares(): array
    {
        return $this->opts['middlewares'] ?? [];
    }

    /**
     * Returns body and params from the request
     * @return array[mixed[]]
     */
    function data()
    {
        return ['params' => $this->params, 'body' => $this->body, 'query' => $this->query];
    }

    /**
     * This method mounts the actual file path to be loaded as an endpoint. Must be set or anything will
     * happen.
     * @param String $page is the domain/file combination where "domain" is the folder and "file" is the actual file name.
     * @param Int $v version control. Default is 2.
     * @return String with full path.
     */
    private function setFilePath(String $page, Int $v = 2)
    {
        return 'src/_ws/v' . $v . '/' . $page . '.php';
    }
}
