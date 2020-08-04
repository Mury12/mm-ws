<?php

namespace MMWS\Model;

/**
 * This is the class to be used into PHP Session management.
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
     * @return mixed session value
     */
    static function get(String $name)
    {
        return isset($_SESSION[pop_encrypt($name)]) ? pop_decode($_SESSION[pop_encrypt($name)]) : false;
    }

    /**
     * Verifies if an authenticated session is registered.
     * @return Bool
     */
    function verify()
    {
        $_ = pop_encrypt('auth');
        return array_key_exists($_, $_SESSION) && $_SESSION[$_];
    }
}
