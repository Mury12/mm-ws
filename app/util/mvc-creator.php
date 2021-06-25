<?php

/**
 * Extracts all the database tables to a MVCE architecture
 */

use MMWS\Handler\DatabaseModelExtractor;
use Dotenv\Dotenv;

/** Composer autoload */
require_once 'vendor/autoload.php';
require_once 'app/partials/_core/application/autoload.php';

/**
 * @var Dotenv\Dotenv $dotenv loads the environment variables in .env
 */
$dotenv = Dotenv::createImmutable('app/../');
$dotenv->load();

require_once 'app/config/db-conf.php';

$dbm = new DatabaseModelExtractor(DB_NAME, 'app/partials/classes', 1);

// It will set to only extract these tables
$dbm->setTables(['users']);

// Starts 
$dbm->generate();
