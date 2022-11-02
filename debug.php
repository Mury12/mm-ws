<?php

/**
 * This file is used to debug specific functionalities 
 */

use MMWS\Handler\DBPuller;
use MMWS\Handler\Connection;

require_once _DEFAULT_APPLICATION_PATH_ . '/functions.php';
require_once('src/config/db-conf.php');
define('DEFAULT_FILE_PATH', 'src/upload/');

// $c_db = new Connection(DB_HOST, DB_NAME, DB_USER, DB_PASS);

// try {
//     /** @var PDO $conn This is the global variable to be used in DB queries */
//     $conn = $c_db->connectMysql();
// } catch (PDOException $e) {
//     send(http_message(500));
//     die;
// }

$dbm = new DBPuller('my_test_db', 'src/classes', 1);

echo "\n ----DEBUG START ----\n";
print_r($dbm->generate());
echo "\n ----DEBUG RESULT END ----\n";
