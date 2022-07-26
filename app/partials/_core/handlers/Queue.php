<?php

namespace MMWS\Handler;

use MMWS\Interfaces\Middleware;

/**
 * Implements a promise queue to execute middlewares in a strict sequence.
 * Every object put in Queue will be returned as a property with it's own class name.
 * 
 * @param String $className the full class name that will be inserted into the queue in VENDOR\Namespace\ClassName
 * @param Array<$className> $promises are the classes and methods to be called. Must be in the following format:
 * array([new Class(), 'method']); If no method is given, 'init' will be called.
 * 
 * -------------
 * 
 * Example Usage:
 * 
 * use MMWS\Handler\Queue;
 * 
 * $queue = new Queue('MMWS\Interfaces\Middleware', array([new Authentication(), 'init']))
 * 
 * $queue->init();
 * 
 * -------------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.9.1-alpha
 */
class Queue
{
    /**
     * @var Array<Middleware> $promises Middlewares that is going to be executed in strict sequence
     */
    private $promises = [];

    /**
     * @var Array<String> $errors maps the indexes that comes with wrong interface type.
     */
    private $errors = [];

    function __construct(String $className, $promises = [])
    {
        foreach ($promises as $promise) {
            $namespace = get_class($promise[0]);
            $class = explode('\\', $namespace);
            $class = array_pop($class);

            if ($promise[0] instanceof $className) {
                $this->promises[$class] = $promise;
            } else {
                $this->errors[] = $class;
            }
        }
    }

    /**
     * Starts the promise queue to execute
     */
    function init()
    {
        global $request;
        foreach ($this->promises as $key => $promise) {
            $this->{$key} = $promise[0]->{$promise[1] ?? 'init'}($request);
        }
        $this->promises = [];
    }

    /**
     * Show the catched errors
     * 
     * @return Array<String>
     */
    function getErrors()
    {
        return $this->errors;
    }

    /**
     * Show the promise queue
     * 
     * @return Array<{$className}>
     */
    function getPromises()
    {
        return $this->promises;
    }
}
