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

    function init()
    {
        foreach ($this->promises as $key => $promise) {
            $this->{$key} = $promise[0]->{$promise[1] ?? 'init'}();
        }
        $this->promises = [];
    }

    function getErrors()
    {
        return $this->errors;
    }

    function getPromises()
    {
        return $this->promises;
    }
}
