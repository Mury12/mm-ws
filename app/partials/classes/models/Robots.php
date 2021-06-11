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
use MMWS\Entity\RobotsEntity;
use MMWS\Interfaces\AbstractModel;

class Robots extends AbstractModel
{

    /**
     * @var String $table the table name for this model;
     */   
    public $table = 'robots';


    public $robotId;
    public $robotNumber;
    public $robotPrice;
    public $robotDescription;
    public $robotSoldCounter;
    

    public function __construct($robotId = null, $robotNumber = null, $robotPrice = null, $robotDescription = null, $robotSoldCounter = null)
    {
        $this->robotId = $robotId;
	    $this->robotNumber = $robotNumber;
	    $this->robotPrice = $robotPrice;
	    $this->robotDescription = $robotDescription;
	    $this->robotSoldCounter = $robotSoldCounter;
	    
        $this->entity = new RobotsEntity($this);
    }
}