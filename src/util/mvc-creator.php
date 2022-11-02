<?php

/**
 * Extracts all the database tables to a MVCE architecture
 */

use MMWS\Handler\DBPuller;
use Dotenv\Dotenv;

/** Composer autoload */
require_once 'vendor/autoload.php';
require_once 'src/config/variables.php';
require_once _DEFAULT_APPLICATION_PATH_ . '/functions.php';

/**
 * @var Dotenv\Dotenv $dotenv loads the environment variables in .env
 */
$dotenv = Dotenv::createImmutable('src/../');
$dotenv->load();

require_once 'src/config/db-conf.php';

$dbm = new DBPuller(DB_NAME, 'src/classes', 1);

// It will set to only extract these tables
// $dbm->setTables(['table_1', 'table_2', 'table_3']);

// Starts 
$dbm->generate();
