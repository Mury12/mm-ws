<?php

/**
 * Extracts all the database tables to a MVCE architecture
 */

use MMWS\Handler\DatabaseModelExtractor;
use Dotenv\Dotenv;

/** Composer autoload */
require_once __DIR__ . '/vendor/autoload.php';
require_once './app/autoload.php';

/**
 * @var Dotenv\Dotenv $dotenv loads the environment variables in .env
 */
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once 'app/config/db-conf.php';

$dbm = new DatabaseModelExtractor(DB_NAME, 'app/partials/classes', 1);

// It will set to only extract these tables
// $dbm->setTables(['table_1', 'table_2', 'table_3']);

// Starts 
$dbm->generate();
