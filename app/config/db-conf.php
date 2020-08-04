<?php

use MMWS\Model\Connection;

/** Uses a local DB config to local db or dev */
if (!file_exists('app/config/local/db-local.php')) {
    /** Cloud DB host IP */
    define('DB_HOST', 'localhost');
    /** Cloud DB NAME */
    define('DB_NAME', 'mm_ws');
    /** Cloud DB username */
    define('DB_USER', 'root');
    /** Cloud DB password */
    define('DB_PASS', 'root');
} else require_once('app/config/local/db-local.php');

/** Creates a connection into DB using PDO 
 * @var Connection $c_db prepared connection object
*/
$c_db = new Connection(DB_HOST, DB_NAME, DB_USER, DB_PASS);

try {
    /** @var PDO $conn This is the global variable to be used in DB queries */
    $conn = $c_db->connectMysql();
} catch (PDOException $e) {
    send(error_message(500));
    die;
}
