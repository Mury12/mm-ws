<?php
/**
 * This file is used to debug specific functionalities 
 */

use MMWS\Handler\ConflexModelExtractor;
use MMWS\Model\Connection;

require_once 'app/autoload.php';
require_once 'app/functions.php';
require_once('app/config/db-conf.php');
define('DEFAULT_FILE_PATH', 'app/_files/');

// $c_db = new Connection(DB_HOST, DB_NAME, DB_USER, DB_PASS);

// try {
//     /** @var PDO $conn This is the global variable to be used in DB queries */
//     $conn = $c_db->connectMysql();
// } catch (PDOException $e) {
//     send(http_message(500));
//     die;
// }

$dbm = new ConflexModelExtractor('mm_dietacerta', 'app/partials/classes',1,'MMWS', 'Conflex_');

echo "\n ----DEBUG START ----\n";
    print_r($dbm->generate());
echo "\n ----DEBUG RESULT END ----\n";
