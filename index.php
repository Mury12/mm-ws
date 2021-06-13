<?php
/**
 * This is the MMWS Api Template
 * Fast api development.
 * 
 * @author Andre Mury <mury63@gmail.com>
 * @version 1.0.1-beta
 * @link https://github.com/mury12/mm-ws
 */

use MMWS\Handler\MMWS;
// Load configurations
require 'app/config/config.php';
// Instantiates the main class
$mmws = new MMWS('index');
// Runs the app
$mmws->amaze();
