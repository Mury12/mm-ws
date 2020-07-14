<?php
/**
 * This file is used to debug specific functionalities 
 */
require_once 'app/autoload.php';
require_once 'app/functions.php';
require_once('app/config/db-conf.php');
define('DEFAULT_FILE_PATH', 'app/_files/');

use Model\Connection;
use Model\User;

$c_db = new Connection(DB_HOST, DB_NAME, DB_USER, DB_PASS);

try {
    /** @var PDO $conn This is the global variable to be used in DB queries */
    $conn = $c_db->connectMysql();
} catch (PDOException $e) {
    send(error_message(500));
    die;
}


echo "\n ----DEBUG START ----\n";
    print_r('debug_var');
echo "\n ----DEBUG RESULT END ----\n";
