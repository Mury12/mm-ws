<?php

namespace MMWS\Model;

class Router
{
    private $base_url = "/";
    private $_routes = array();
    private $headers = array();

    function __construct()
    {
        $this->headers['Access-Control-Allow-Headers'] = 'content-type, Content-Type, user-addr, authorization';
        $this->headers['Access-Control-Allow-Methods'] = '*';
        $this->headers['Access-Control-Allow-Origin'] = HTTP_CORS_URI;
        $this->headers['Content-Type'] = 'application/json';
    }

    /**
     * Creates the routes contained in the arrays
     * @param Array $routes route collection
     */
    function createRoutes(array $routes)
    {
        $this->_routes = $routes;
    }

    function getRoutes()
    {
        print_r($this->_routes);
    }

    /**
     * Adds headers to the route.
     * @param mixed $rule can be both String containing the rule name or an array in format $arr['rulename'] = 'arg'
     * @param String $arg  is the value to the rule name such as 'application/json'
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

    function headers()
    {
        foreach ($this->headers as $rule => $arg) {
            header($rule . ': ' . $arg);
        }
    }


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

    function getPage($slug = null)
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
            setErrorCode(404);
            return $this->getErrorPage('404');
        }
    }
}
