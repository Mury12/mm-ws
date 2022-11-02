<?php

namespace MMWS\Middleware;

use DateTime;
use MMWS\Factory\RequestExceptionFactory;
use MMWS\Handler\Request;
use MMWS\Handler\RequestException;
use MMWS\Handler\SESSION;
use MMWS\Interfaces\Middleware;

/**
 * Implements a 429 error when client tries
 * to request a resource too much times in
 * less than the specified interval
 * 
 * @param String $name the request name
 * @param Int $timeout time in seconds to REDO the request. Default is 10 seconds 
 * @param Int $interval interval between requests. Default is 1 second
 */
class Throttle implements Middleware
{
    private $name = '_session_timeout_throttle';

    /**
     * @var int $timeout timeout to clean cache
     */
    public $timeout;

    /**
     * @var int $interval interval between requests
     */
    public $requests;

    /**
     * @var int $period time to allow user again.
     */
    public $period;


    /**
     * @param int $requests amount of requests per period
     * @param int $period time to perform the maximum number of requests
     * @param int $timeout timeout to clean cache
     */
    function __construct($requests = 10, $period = 60, $timeout = 120)
    {
        $this->timeout = $timeout;
        $this->requests = $requests;
        $this->period = $period;
    }

    function init(Request $request)
    {
        try {
            return $this->action();
        } catch (RequestException $e) {
            set_http_code($e->getCode());
            $error = json_decode($e->getMessage(), true);
            if (is_array($error))
                send($error);
            die();
        }
    }

    function action()
    {
        if ($current = json_decode(SESSION::get($this->name), true)) {

            $now = new \DateTime();
            $old = new DateTime($current[0]['date']);
            $diff = $now->getTimestamp() - $old->getTimestamp();
            $timeout = SESSION::get($this->name . '__timeout');

            if ($timeout && $timeout >= $diff) {
                SESSION::add($this->name . '__timeout', ($this->timeout - $diff));
                throw RequestExceptionFactory::tmr429(($this->timeout - $diff));
            }

            if ($diff <= $this->period && $current[1] <= $this->requests)
                $this->put($current);
            elseif ($diff >= $this->period) {
                $this->put();
            } else {
                SESSION::add($this->name . '__timeout', ($this->timeout - $diff));
                throw RequestExceptionFactory::tmr429(($this->timeout - $diff));
            }
        } else {
            $this->put();
        }
        return true;
    }

    /**
     * @param array $current the current result
     */
    private function put(array $current = null)
    {
        if ($current) {
            $current[1]++;
            SESSION::add($this->name, json_encode($current));
        } else {
            $now = new \DateTime();
            SESSION::add($this->name, json_encode([$now, 1]));
        }
    }
}
