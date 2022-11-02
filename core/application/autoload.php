<?php

/**
 * This file loads all the class files. 
 */

namespace MMWS;

require_once 'functions.php';

/** Global functions*/

class Autoloader
{

    static function register()
    {
        spl_autoload_register(function ($class) {
            $core = _DEFAULT_CORE_PATH_;
            $names = explode('\\', $class);
            $vendor = array_unshift($names);
            $filename = array_pop($names);
            $context = array_pop($names);
            $path = $core . '/' . $context . '/' . $filename  . '.php';
            require $path;
        });
    }
}

Autoloader::register();
