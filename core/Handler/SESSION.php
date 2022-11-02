<?php

namespace MMWS\Handler;

use Exception;
use MMWS\Factory\RequestExceptionFactory;

/**
 * This is the class to be used into PHP Session management.
 * 
 * ----------
 * 
 * Example Usage:
 * 
 * SESSION::init();
 * 
 * SESSION::add('auth', true);
 * 
 * !SESSION::get('auth') ? SESSION::done() : $endpoint->render();
 * 
 * ----------
 * 
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.0.1-alpha
 */
class SESSION
{
    /**
     * Starts the sessions. Alias for session_start()
     */
    static function init()
    {
        session_start();
    }

    /**
     * Destroys the session. Alias for session_destroy()
     */
    static function done()
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Adds a property to the session encripting its name and value
     * @param String $name the index value
     * @param mixed $value the value to be stored
     */
    static function add(String $name, $value)
    {
        $_name = pop_encrypt($name);
        $v = pop_encode($value);
        $_SESSION[$_name] = $v;
    }

    /**
     * Gets the sessions value for its name
     * @param String $name the session index
     * 
     * @return mixed session value
     */
    static function get(String $name)
    {
        return isset($_SESSION[pop_encrypt($name)]) ? pop_decode($_SESSION[pop_encrypt($name)]) : false;
    }


    static function loadCookies()
    {
        $cookies = $_COOKIE;
        if ($cookies && array_key_exists('app-token', $cookies)) {
            $jwt = $cookies['app-token'];
            try {

                if (JWTHandler::verify($jwt) && !SESSION::get('@app:jwt')) {
                    SESSION::add('@app:jwt', $jwt);
                }
            } catch (Exception $e) {
                setcookie('app-token', $jwt, time());
                throw RequestExceptionFactory::create('Token de acesso inválido.', 401);
            }
        }
    }

    /**
     * Verifies if an authenticated session is registered.
     * 
     * @return Bool
     */
    function verify()
    {
        $_ = pop_encrypt('auth');
        return array_key_exists($_, $_SESSION) && $_SESSION[$_];
    }
}
