<?php

use MMWS\Handler\SESSION;
use MMWS\Interfaces\Middleware;

/**
 * Implements request caching with timeout
 * @param String $name the request name
 * @param Int $timeout time in seconds to REDO the request. Default is 10 seconds 
 * @param Int $interval interval between requests. Default is 1 second
 */
class CACHE implements Middleware
{


    /**
     * @var Int $timeout timeout to clean cache
     */
    public static $timeout = 10;

    /**
     * @var Int $interval interval between requests
     */
    public static $interval = 1;

    /**
     * @var Array $cache is the cached content
     */
    public static $cache;

    private $session = [];

    function init()
    {
        return $this->action();
    }

    function action()
    {
    }


    /**
     * @param String $name the request name
     */
    static function check($name)
    {

        $cachedName = $name . '_cache';
        if (SESSION::get($cachedName)) {
            $cached = json_decode(SESSION::get($cachedName), true);

            $now = new DateTime();

            $diff = date_diff($now, new DateTime($cached['time']['date']))->format('%s');
            if ($diff < self::$timeout) {
                return $cached['result'];
            }
        }
        return false;
    }

    /**
     * @param String $name the request name
     */
    static function put($result, $name)
    {
        $cachedName = $name . '_cache';
        $now = new DateTime();
        $request = array(
            'time' => $now,
            'result' => $result
        );
        SESSION::add($cachedName, json_encode($request));
    }

    private function updateTimeout()
    {
    }

    private function updateInterval()
    {
    }
}
