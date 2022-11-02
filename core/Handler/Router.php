<?php

namespace MMWS\Handler;

/**
 * Manage the server routes
 * This is the main router handling every route created
 * in the router files
 * 
 * @param Array<Endpoint> $routes the indexed routes array with its properties
 * 
 * For examples see the router files in src/routers
 * 
 * ----------
 * 
 * Example Usage:
 * 
 * use MMWS\Handler\Router;
 * 
 * $router = new Router();
 * 
 * $router->init(require_once(src/routes.php));
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
    private function bindParams(array $curRoute, &$matches)
    {
        $params = array();
        $m = $matches;
        while (sizeof($m) > sizeof($curRoute['params'])) {
            array_pop($m);
        }

        // if (sizeof($matches) <= sizeof($curRoute['params'])) {
        foreach ($curRoute['params'] as $key => $param) {
            $params[$param] = array_shift($m);
            array_shift($matches);
        }
        return $params;
        // }
        // return false;
    }

    /**
     * Maps the url params based on the matches
     * 
     * @param Array $matches the matched params
     * @return mixed
     */
    private function bind(array $matches)
    {
        $curRoute = $this->_routes;
        $params = [];
        foreach ($matches as $key => $match) {
            if (array_key_exists('params', $curRoute)) {
                $params = array_merge($this->bindParams($curRoute, $matches), $params);

                if (array_key_exists($match, $curRoute)) {
                    $curRoute = $curRoute[$match];
                }

                if (!sizeof($matches)) break;
            }
            if (array_key_exists($match, $curRoute)) {
                $curRoute = $curRoute[$match];
            } elseif (!sizeof($matches)) {
                $this->send404();
            } elseif (
                sizeof($matches) && !array_key_exists($match, $curRoute)
            ) {
                array_unshift($matches);
            } else {
                $this->send404();
            }
            //                                                                                          V-- skipping after this
            // It is skipping if more than 1 param is given to a middle route like /:company/:user/something/:soomethingId
            if (array_key_exists($key, $matches) && $matches[$key] === $match)
                unset($matches[$key]);
        }
        // Checks if the matches are over and if it is, checks if a '/' exists, meaning that this could be a root route
        if (sizeof($matches) > 0) {
            $this->send404();
        } else if (array_key_exists('/', $curRoute)) {
            // and if it is, use it.
            $curRoute = $curRoute['/'];
        }
        if (array_key_exists('body', $curRoute)) {
            $curRoute['body']->setEnv($params);
            return $curRoute;
        } else {
            $this->send404();
        }
    }

    function getErrorPage(String $err_code)
    {
        return $this->_routes['error'][$err_code]['body'];
    }

    /**
     * Sends a 404 error and exits the app
     */
    private function send404()
    {
        send(http_message(404));
        die();
    }

    /**
     * Returns the referred endpoint using the requested URI
     * 
     * @return MMWS\Handler\Endpoint the endpoint or error page
     */
    function get()
    {
        $uri = substr($_SERVER['REQUEST_URI'], 1);
        $uri = explode('?', $uri);
        $route = $uri[0];
        $route = rtrim($route, "/");
        $matches = explode('/', $route);

        $route = $this->bind($matches);

        if ($route) {
            if (isset($uri[1])) {
                $route['body']->setQueryParams();
            }
            return $route['body'];
        } else {
            $this->send404();
        }
    }
}
