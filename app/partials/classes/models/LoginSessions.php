<?php
/**
 * This file was automatically generated by the Awesome Conflex Webservice Model Generator Module
 * based in MYSQL/Mariadb database tables. After the generation you can modify
 * this model to fill with the best methods you think it should have.
 * 
 * 
 * Take care of this template and abuse of this.
 * 
 * Thank you for using it. github.com/mury12
 * 
 */
 
namespace MMWS\Model;
use MMWS\Entity\LoginSessionsEntity;
use MMWS\Interfaces\AbstractModel;

class LoginSessions extends AbstractModel
{

    /**
     * @var String $table the table name for this model;
     */   
    public $table = 'login_sessions';


    public $id;
    public $loginTime;
    public $userEmail;
    public $internalToken;
    public $validated;
    public $sentToken;
    

    public function __construct($id = null, $loginTime = null, $userEmail = null, $internalToken = null, $validated = null, $sentToken = null)
    {
        $this->id = $id;
	    $this->loginTime = $loginTime;
	    $this->userEmail = $userEmail;
	    $this->internalToken = $internalToken;
	    $this->validated = $validated;
	    $this->sentToken = $sentToken;
	    
        $this->entity = new LoginSessionsEntity($this);
    }
}