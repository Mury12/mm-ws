<?php

namespace MMWS\Handler;

use MMWS\Middleware\Cache;
use MMWS\Factory\RequestExceptionFactory;
use MMWS\Handler\Queue;
use MMWS\Middleware\Authentication;
use MMWS\Handler\Request;
use MMWS\Abstracts\View;

/**
 * Creates endpoints and configure it.
 * All endpoints created will need to be set
 * to any HTTP method GET|DELETE|POST|PATCH|PUT
 * and will be used to set routes.
 * 
 * ----------
 * 
 * Example Usage:
 * 
 * use MMWS\Handler\Endpoint;
 * 
 * return ['ws' => ['body' => $e = new Endpoint(), $e->get('domain/file', 'method')->permission('auth')]]
 * 
 * ----------
 * 
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.0.1-alpha
 */
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
     * @var Array $query the query params in the url
     */
    public $query = array();

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
     * 
     * @return MMWS\Handler\Endpoint self
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
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function post($page, $procedure, array $opts = [])
    {
        $this->request->add("POST", $page, $procedure, $opts);
        return $this;
    }
    /**
     * This method only sets the request type to "PATCH" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function patch($page, $procedure, array $opts = [])
    {
        $this->request->add("PATCH", $page, $procedure, $opts);
        return $this;
    }
    /**
     * This method only sets the request type to "PUT" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function put($page, $procedure, array $opts = [])
    {
        $this->request->add("PUT", $page, $procedure, $opts);
        return $this;
    }
    /**
     * This method only sets the request type to "GET" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function get($page, $procedure, array $opts = [])
    {
        $this->request->add("GET", $page, $procedure, $opts);
        return $this;
    }

    /**
     * Adds a page that instantiates a socket
     * @param String $page the actual filename in the files folder
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function socket($page)
    {
        $this->request->add("GET", $page, '', []);
        return $this;
    }
    /**
     * This method only sets the request type to "DELETE" and 
     * the procedure or method to be used, contained in the
     * set endpoint.
     * @param String $page the actual filename in the files folder
     * @param String $procedure the method to be called when the route is requested
     * @param Int $v is the version control, default is 2.
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function delete($page, $procedure, array $opts = [])
    {
        $this->request->add("DELETE", $page, $procedure, $opts);
        return $this;
    }

    /**
     * This method gets the body params
     */
    private function getRequestParams()
    {
        global $request;
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        if (str_in($method, ['POST', 'PUT', 'PATCH'])) {
            $fn = strtolower($method) . '_params';
            $request->setBody($fn());
        }
        return $method;
    }


    /**
     * Renders the file into an endpoint page including the requested params, url params or
     * env params put into the router.
     * 
     * @return file the required file
     */
    public function render()
    {

        global $request, $middlewares;
        $method = $this->getRequestParams();

        if ($req = $this->request->get($method)) {

            $request->setMethod($method);
            $request->setParams($this->getEnv());
            $request->setQuery($this->query);
            $request->setProcedure($req['procedure']);
            $request->setOpts($req['opts']);

            /**
             * @var Queue $middleware MMWS\Interfaces\Middleware queue to be executed BEFORE page rendering
             */
            $middleware = new Queue(
                'MMWS\Interfaces\Middleware',
                array_merge(
                    $middlewares,
                    $this->middlewares,
                    $request->getMiddlewares()
                )
            );
            $middleware->init();

            if (file_exists($req['page'])) {
                $view = require_once $req['page'];
            } else {
                throw RequestExceptionFactory::create('The requested file does\'not exist.', 404);
            };
            return $this->checkAndRender($request, $view);
        } else {
            die(send(http_message(401)));
        }
    }

    private function checkAndRender(Request $request, View $view)
    {
        /** Check if this endpoit is caching requests */
        if ($this->caching && $_SERVER['REQUEST_METHOD'] === 'GET') {
            global $cached;
            try {
                $cached = Cache::check($request->getProcedure());
                /**
                 * Caches requests if caching is enabled
                 */
                if (!$cached) {
                    /**
                     * @var mixed $m result from the procedure
                     */
                    $m = $view->call();
                    Cache::put($m, $request->getProcedure());
                }
                $m = $m ?? $cached;
            } catch (\Exception $e) {
                if ($e instanceof RequestException) throw $e;
                throw RequestExceptionFactory::create($e->getMessage(), $e->getCode());
            }
        } else {
            $m = $view->call();
        }
        return $m;
    }

    function setQueryParams()
    {
        $this->query = $_GET;
    }

    /**
     * Sets the environment variables that will be used in this endpoint.
     * @param Array $env the indexed variables and its values.
     * @example $e->setEnv(['name' => 'Jon Garret', 'age' => '32']); (in the router configurations)
     * Then you can use it inside an endpoint just calling their names $name or $age.
     * 
     * @return MMWS\Handler\Endpoint self
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
     * 
     * @return array indexed variables => values. Usually extracted in the render() method.
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * Set or returns if this is an Endpoint page. Will be deprecated soon.
     * @deprecated in v0.9.1 
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
     * @param Int $level as NOT_AUTH = 0;
     *                      AUTH = 1;
     *                      ANY_ACCESS = 2;
     * _You can also user Authentication::AUTH|NOT_AUTH|ANY_ACCESS constants_
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function permission(Int $level = Authentication::NOT_AUTH)
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
     * 
     * @return MMWS\Handler\Endpoint self
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
     * Gets the file located in 'src/pieces' that can be put to complement another endpoint.
     * @deprecated in v1.0.1
     * @var file filename with no extension.
     * 
     * @return String the file contents
     */
    public function getFilePartial($file)
    {
        return \file_get_contents('src/pieces/' . $file . '.php');
    }

    /**
     * Adds middlewares to this endpoint
     * 
     * @param Array<Middleware> array of middlewares
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function addMiddleware(array $middlewares)
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }

    /**
     * Sets if the caching is enabled to this endpoint
     * 
     * @param Bool $cache 
     * 
     * @return MMWS\Handler\Endpoint self
     */
    public function cache(Bool $cache = true)
    {
        $this->caching = $cache;
        return $this;
    }
}
