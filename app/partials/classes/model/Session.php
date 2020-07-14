<?php

namespace MMWS\Model;

abstract class SESSION
{
    static function init()
    {
        session_start();
    }

    static function done()
    {
        $_SESSION = [];
        session_destroy();
    }

    static function add(String $name, $value)
    {
        $_name = pop_encrypt($name);
        $v = pop_encode($value);
        $_SESSION[$_name] = $v;
    }

    static function get(String $name)
    {
        return isset($_SESSION[pop_encrypt($name)]) ? pop_decode($_SESSION[pop_encrypt($name)]) : false;
    }

    function verify()
    {
        $_ = pop_encrypt('auth');
        return array_key_exists($_, $_SESSION) && $_SESSION[$_];
    }
}
