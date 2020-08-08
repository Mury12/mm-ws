<?php

namespace MMWS\Model;

use MMWS\Entity\UserEntity;
/**
 * It's a default user class
 * @param Array $request the request array containing indexed Model\User properties
 *      Make sure to write this props exaclty as the database or making a FromTo procedure in constructor.
 */
class User
{
    /**
     * @var Array $request request array contaning Model\User properties
     */
    private $request;

    /**
     * @var UserEntity $entity user entity
     */
    private $entity;

    /**
     * @var Integer $ID user id 
     */
    public $ID;

    /**
     * @var String $username user name
     */
    public $username;

    /**
     * @var String $email user email
     */
    public $email;

    /**
     * @var String $password is sha256 encrypted in constructor
     */
    public $password;

    /**
     * @param Array $request request array contaning Model\User properties
     */
    function __construct(array $request)
    {
        if (is_array($request)) {
            foreach ($request as $key => $val) {
                $this->{$key} = $val;
            }
        }

        $this->entity = new UserEntity($this);

        if (!$this->ID && SESSION::get('userID'))
            $this->ID = SESSION::get('userID');

        $this->password = hash('sha256', $this->password ?? '') ?? null;
    }

    /**
     * Verifies if the user is authenticated based in its token
     */
    function verify()
    {
        return $this->entity->verify();
    }

    /**
     * Verifies if the user is logged in based in PHP session
     */
    function isLoggedIn()
    {
        return SESSION::get('auth');
    }

    /**
     * Performs a token removal and session cleaning
     */
    function logOut()
    {
        $this->entity->logout();
    }

    /**
     * Performs a user->password bind in database to authenticate
     * and generate a new token
     */
    function bindUserPassword()
    {
        return $this->entity->bindUserPassword();
    }

    /**
     * Calls the procedure to save an entity into the database
     */
    public function save()
    {
        return $this->entity->save();
    }
}
