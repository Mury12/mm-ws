<?php

use MMWS\Handler\MMWS;

require 'app/config/config.php';
// Instantiates the main class
$mmws = new MMWS($_ENV['APP_ENV'] ?? 'development', 'index');
// Runs the app
$mmws->amaze();
