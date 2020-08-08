<?php

namespace MMWS\Controller;

use MMWS\Model\{
    SESSION,
    User
};

/**
 * Requires Model\User to be valid.
 * @param Array $request with valid indexes [String username], [String password], [String email] and an optional [Integer ID]
 */
class UserController
{

    private $usr;

    function __construct($request = null)
    {
        if ($request != null) {
            $this->usr = new User($request);
        }
    }

    private function register(String $session_token)
    {
        SESSION::add('auth', true);
        SESSION::add('session_token', $session_token);
        return false;
    }

    /**
     * Authenticates an user
     */
    function login()
    {
        if ($session_token = $this->usr->bindUserPassword()) {
            $this->register($session_token);
            return $session_token;
        }
        return false;
    }


    /**
     * Creates a new user based on Model\User properties.
     * @return String as the user token or error message.
     */
    function createUser()
    {
        return $this->usr->save();
    }

    /**
     * Verifies an authentication token
     * @return Bool
     */
    function verify()
    {
        $v = $this->usr->verify();

        if ($v) {
            SESSION::add('userID', $v);
            return true;
        }
        return false;
    }

    /**
     * Sign out an user.
     */
    function logout()
    {
        SESSION::done();

        $this->usr->logout();
        return true;
    }
}
