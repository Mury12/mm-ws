<?php

/**
 * Extracts all the database tables to a MVCE architecture
 */

use MMWS\Handler\DatabaseModelExtractor;

require_once 'app/autoload.php';
require_once 'app/functions.php';
require_once('app/config/db-conf.php');

$dbm = new DatabaseModelExtractor(DB_NAME, 'app/partials/classes', 1);

$dbm->generate();
