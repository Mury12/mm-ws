<?php

use MMWS\Model\Connection;

/** Uses a local DB config to local db or dev */
if (!file_exists('app/config/local/db-local.php')) {
    /** Cloud DB host IP */
    define('DB_HOST', $_ENV['DB_HOST']);
    /** Cloud DB NAME */
    define('DB_NAME', $_ENV['DB_NAME']);
    /** Cloud DB username */
    define('DB_USER', $_ENV['DB_USER']);
    /** Cloud DB password */
    define('DB_PASS', $_ENV['DB_PASS']);
} else require_once('app/config/local/db-local.php');

/** Creates a connection into DB using PDO 
 * @var Connection $c_db prepared connection object
*/
$c_db = new Connection(DB_HOST, DB_NAME, DB_USER, DB_PASS);

try {
    /** @var PDO $conn This is the global variable to be used in DB queries */
    $conn = $c_db->connectMysql();
} catch (PDOException $e) {
    send(http_message(500));
    die;
}
