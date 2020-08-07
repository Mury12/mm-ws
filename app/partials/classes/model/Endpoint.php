<?php

namespace MMWS\Model;

use MMWS\Handler\Queue;
use MMWS\Middleware\Authentication;
use MMWS\Model\Request;

class Endpoint
{

    /**
     * @var Array $env are the environment variables that will be extracted to the page body.
     */
    private $_env = array();

    /**
     * @var Bool $api sets if the page is an endpoint
     * @deprecated in v1.0.1
     */
    private $api = true;

    /**
     * @var String $access sets the access type to the endpoint. 
     * - any means that anybody can request to this page
     * - auth means that only authenticated requests can request
     * - not means that only not authenticated requests can request
     */
    private $access = 'any';

    /**
     * @var String $route is the URI to the endpoint, automatically set in the router configuration.
     */
    private $route;

    /**
     * @var Request $request is the Request object that handles request configurations
     */
    private $request;

    /**
     * @var String $procedure is the procedure that will be called in the mounted endpoint. 
     * This is set right after the URL is called and Endpoints tries to render.
     */
    public $procedure;

    /**
     * @var Array $body is the body params catched from get_`reqmethod`() so it can be accessed like $endpoint->body params.
     */
    public $body = array();

    /**
     * @var Array<Middleware> $middlewares middlewares injected to the page. This will be executed strictly after the default middlewares.
     */
    public $middlewares = array();

    /**
     * @var Bool $caching enables or disables request caching
     */
    public $caching = false;

    function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Return files inserted in @method appendPartials()
     * @return file de arquivos
     */
    public function getPartials()
    {
        return require_once $this->partials;
    }

    /**
     * Just stets an error file (not working just now)
     * @param String $page the filename
     */
    public function error($page)
    {
        $this->request->add("ERROR", $page, '');
        return $this;
    }
    /**
     * This method only sets the request type to "POST" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     */
    public function post($page, $procedure, Int $v = 2)
    {
        $this->request->add("POST", $page, $procedure);
        return $this;
    }
    /**
     * This method only sets the request type to "PATCH" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     */
    public function patch($page, $procedure, Int $v = 2)
    {
        $this->request->add("PATCH", $page, $procedure);
        return $this;
    }
    /**
     * This method only sets the request type to "PUT" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     */
    public function put($page, $procedure, Int $v = 2)
    {
        $this->request->add("PUT", $page, $procedure);
        return $this;
    }
    /**
     * This method only sets the request type to "GET" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     */
    public function get($page, $procedure, Int $v = 2)
    {
        $this->request->add("GET", $page, $procedure);
        return $this;
    }
    /**
     * This method only sets the request type to "DELETE" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     */
    public function delete($page, $procedure, Int $v = 2)
    {
        $this->request->add("DELETE", $page, $procedure);
        return $this;
    }

    /**
     * This method gets the body params
     */
    private function getRequestParams()
    {
        global $body;
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        if (str_in($method, ['POST', 'PUT', 'PATCH'])) {
            $fn = strtolower($method) . '_params';
            $body = $fn();
        }
        return $method;
    }


    /**
     * Renders the file into an endpoint page including the requested params, url params or
     * env params put into the router.
     * @return file the required file
     */
    public function render()
    {
        /**
         * @var Queue $middleware MMWS\Interfaces\Middleware queue to be executed AFTER the page rendering
         */
        $middleware = new Queue(
            'MMWS\Interfaces\Middleware',
            array_merge(
                array(
                    [new Authentication()],
                ),
                $this->middlewares
            )
        );
        $middleware->init();

        if ($middleware->Authentication) {
            global $params;
            global $procedure;
            $method = $this->getRequestParams();

            if ($req = $this->request->get($method)) {
                $params = $this->getEnv();
                $procedure = $req['procedure'];
                extract($params);
                return file_exists($req['page']) ? require_once $req['page'] : die(send(error_message(500)));
            } else {
                die(send(error_message(405)));
            }
        }else{
            die(send(error_message(403)));
        }
    }

    /**
     * Sets the environment variables that will be used in this endpoint.
     * @param Array $env the indexed variables and its values.
     * @example $e->setEnv(['name' => 'Jon Garret', 'age' => '32']); (in the router configurations)
     * Then you can use it inside an endpoint just calling their names $name or $age.
     */
    public function setEnv(array $env)
    {
        foreach ($env as $key => $val) {
            $this->_env[$key] = $val;
        }
        return $this;
    }

    /**
     * Gets all the environment variables set in @method setEnv().
     * @return array of indexed variables => values. Usually extracted in the render() method.
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * Set or returns if this is an Endpoint page. Will be deprecated soon.
     * @deprecated in v1.0.1
     */
    public function isApi($bool = true)
    {
        if ($bool) {
            $this->api = true;
        }
        return $this->api;
    }

    /**
     * Sets the user permission to access the endpoint. If not set, default is "any".
     * @param String $level auth|not|any 
     */
    public function permission(String $level)
    {
        $this->access = $level;
        return $this;
    }

    /**
     * Gets the type of access in this page, set in @method permission()
     */
    public function getAccessLevel()
    {
        return $this->access;
    }

    /**
     * Sets the route name to the endpoint
     * @param String $route the actual route name
     */
    public function setRouteName($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Gets the route name set in this page.
     */
    public function getRouteName()
    {
        return $this->route;
    }

    /**
     * Gets the file located in 'app/partials/pieces' that can be put to complement another endpoint.
     * @deprecated in v1.0.1
     * @var file filename with no extension.
     * @return String the file contents
     */
    public function getFilePartial($file)
    {
        return \file_get_contents('app/partials/pieces/' . $file . '.php');
    }

    public function addMiddleware(array $middlewares)
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }

    public function cache(Bool $cache = true)
    {
        $this->caching = $cache;
    }
}
