<?php
namespace MMWS\Model;

use \PDO;

class Connection
{
    private $host;
    private $dbName;
    private $dbUser;
    private $dbPass;

    function __construct($host, $dbName, $dbUser, $dbPass = null)
    {
        $this->host     = $host;
        $this->dbName   = $dbName;
        $this->dbUser   = $dbUser;
        $this->dbPass   = $dbPass;
    }

    function connectMysql()
    {
        try{
            $conn = new PDO
                (
                    'mysql:host='.$this->host.
                    ';dbname='.$this->dbName, 
                    $this->dbUser, $this->dbPass,
                    
                );
        }catch(\PDOException $e){
            print_r($e->getMessage());
        }

        return $conn;
    }
}