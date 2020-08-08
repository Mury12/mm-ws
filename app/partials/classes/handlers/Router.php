<?php

namespace MMWS\Handler;

/**
 * Manage the server routes
 * This is the main router handling every route created
 * in the router files
 * 
 * @param Array<Endpoint> $routes the indexed routes array with its properties
 * 
 * For examples see the router files in app/routers
 * 
 * ----------
 * 
 * Example Usage:
 * 
 * use MMWS\Handler\Router;
 * 
 * $router = new Router();
 * 
 * $router->init(require_once(app/routes.php));
 * 
 * $endpoint = $router->get();
 * 
 * $endpoint->render(); // will render the endpoint contained in the route
 * 
 * ----------
 * 
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.0.1-alpha
 */
class Router
{
    private $base_url = "/";
    private $_routes = array();
    private $headers = array();

    function __construct()
    {
        $this->headers['Access-Control-Allow-Headers'] = HTTP_ALLOW_HEADERS;
        $this->headers['Access-Control-Allow-Methods'] = HTTP_ALLOW_METHODS;
        $this->headers['Access-Control-Allow-Origin'] = HTTP_CORS_URI;
        $this->headers['Content-Type'] = HTTP_CONTENT_TYPE;
    }

    /**
     * Creates the routes contained in the arrays
     * @param Array $routes route collection
     */
    function init(array $routes)
    {
        $this->_routes = $routes;
    }

    /**
     * Returns all the previously configured routes
     * and its properties
     * 
     * @return Array<mixed> the routes
     */
    function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Adds headers to the route.
     * @param mixed $rule can be both String containing the rule name or an array in format $arr['rulename'] = 'arg'
     * @param String $arg  is the value to the rule name such as 'application/json'
     * 
     * @return Model/Router current instance
     */

    function addHeaders($rule, String $arg = null)
    {
        if (is_array($rule)) {
            array_merge($this->headers, $rule);
        } else {
            array_push($this->headers[$rule], $arg);
        }
        return $this;
    }

    /**
     * Add headers to the router
     */
    function headers()
    {
        foreach ($this->headers as $rule => $arg) {
            header($rule . ': ' . $arg);
        }
    }

    /**
     * Bind url params
     * @param Iterable $curRoute current analysed route
     * @param Array<String> $matches the matched params found
     * 
     * @return Array<String>|false the indexed params or false if not succeed
     */
    private function bindParams($curRoute, &$matches)
    {
        $params = array();
        if (sizeof($matches) <= sizeof($curRoute['params'])) {
            foreach ($curRoute['params'] as $key => $param) {
                $params[$param] = array_shift($matches);
            }
            $curRoute['body']->setEnv($params);
            return $params;
        }
        return false;
    }

    /**
     * Maps the url params based on the matches
     * 
     * @param Array $matches the matched params
     */
    private function bind(array $matches)
    {
        $curRoute = $this->_routes;
        $params = [];
        $skip = false;
        foreach ($matches as $key => $match) {
            $i = 0;
            $paramCount = 0;

            if (array_key_exists('params', $curRoute) && !$skip) {
                $curRoute['params'] = $this->bindParams($curRoute, $matches);
                $skip = true;
                if(sizeof($matches) == 0) break;
            }

            if (array_key_exists($match, $curRoute)) {
                $curRoute = $curRoute[$match];
            } else {
                $curRoute = false;
                break;
            }
            unset($matches[$key]);
        }
        return $curRoute;
    }

    function getErrorPage(String $err_code)
    {
        return $this->_routes['error'][$err_code]['body'];
    }

    /**
     * Returns the referred endpoint using the requested URI
     * 
     * @return MMWS\Handler\Endpoint the endpoint or error page
     */
    function get()
    {
        $route = substr($_SERVER['REQUEST_URI'], 1);
        // $route = explode('?', $slug)[0];
        $route = rtrim($route, "/");
        $matches = explode('/', $route);


        $route = $this->bind($matches);

        if ($route) {
            // print_r($route);
            return $route['body'];
        } else {
            set_http_code(404);
            return $this->getErrorPage('404');
        }
    }
}
