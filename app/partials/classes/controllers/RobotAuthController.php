<?php
/**
 * This file was automatically generated by the Awesome Conflex API Model Generator Module
 * based in MYSQL/Mariadb database tables. After the generation you can modify
 * this model to fill with the best methods you think it should have.
 * 
 * 
 * Take care of this template and abuse of this.
 * 
 * Thank you for using it. github.com/mury12
 * 
 */
 
namespace MMWS\Controller;

use MMWS\Model\RobotAuth;
use MMWS\Interfaces\AbstractController;

class RobotAuthController extends AbstractController
{
    public $model;

    public function __construct(array $data)
    {
        $model = new RobotAuth();
        foreach ($data as $key => $prop) {
            if (property_exists($model, $key)) {
                $model->{$key} = $prop;
            }
        }
        $this->model = $model;
    }

}
