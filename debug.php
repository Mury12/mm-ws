<?php

/**
 * This file is used to debug specific functionalities 
 */

require_once 'app/autoload.php';
require_once 'app/functions.php';
require_once('app/config/db-conf.php');
require_once('app/config/variables.php');
define('DEFAULT_FILE_PATH', 'app/_files/');

// $c_db = new Connection(DB_HOST, DB_NAME, DB_USER, DB_PASS);

// try {
//     /** @var PDO $conn This is the global variable to be used in DB queries */
//     $conn = $c_db->connectMysql();
// } catch (PDOException $e) {
//     send(http_message(500));
//     die;
// }

// $dbm = new DatabaseModelExtractor('mm_dietacerta', 'app/partials/classes', 1);

// $jwt = new JWT();

echo "\n ----DEBUG START ----\n";
print_r(password_hash('123456', PASSWORD_BCRYPT, ['cost' => 12]));
echo "\n ----DEBUG RESULT END ----\n";
