<?php

namespace MMWS\Model;

use MMWS\Entity\UserEntity;

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

    function verify()
    {
        return $this->entity->verify();
    }

    function isLoggedIn()
    {
        return SESSION::get('auth');
    }

    function logOut()
    {
        $this->entity->logout();
    }

    function bindUserPassword()
    {
        return $this->entity->bindUserPassword();
    }

    public function save()
    {
        return $this->entity->save();
    }
}
