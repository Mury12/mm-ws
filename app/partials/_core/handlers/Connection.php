<?php

namespace MMWS\Handler;

use \PDO;

/**
 * Performs a database connection
 * 
 * @param String $host the hostname
 * @param String $dbName the database name
 * @param String $dbUser the database username
 * @param String|null $dbPass the database password
 * 
 * -------------
 * 
 * Example Usage:
 * 
 * use MMWS\Handler\Connection;
 * 
 * $db = new Connection('localhost', 'mm_ws', 'root', 'root');
 * 
 * $conn = $db->mysql();
 * 
 * $q = $conn->prepare('SELECT * FROM table');
 * 
 * $r = $q->execute();
 * 
 * $r = $r->fetchAll(PDO::FETCH_NAMED);
 * 
 * -------------
 * @package MMWS
 * @author Andre Mury <mury_gh@hotmail.com>
 * @version MMWS^0.0.1-alpha
 */

class Connection
{
    /**
     * @var String $host the hostname
     */
    private $host;

    /**
     * @var String $dbName the database name
     */
    private $dbName;

    /**
     * @var String $dbUser the database username
     */
    private $dbUser;

    /**
     * @param String|null $dbPass the database password
     */
    private $dbPass;

    function __construct($host, $dbName, $dbUser, $dbPass = null)
    {
        $this->host     = $host;
        $this->dbName   = $dbName;
        $this->dbUser   = $dbUser;
        $this->dbPass   = $dbPass;
    }

    /**
     * Connects to MySql database
     * 
     * @return PDO|Bool the connection itself or false if not succeed
     */
    function mysql()
    {
        try {
            $conn = new PDO(
                'mysql:host=' . $this->host .
                    ';dbname=' . $this->dbName,
                $this->dbUser,
                $this->dbPass,

            );
            return $conn;
        } catch (\PDOException $e) {
            report(['type' => 'Database error', 'message' => $e->getMessage()]);
            throw $e;
        }
        return false;
    }
}
