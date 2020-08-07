<?php

/** Loads config.php */
require_once 'app/config.php';

/** Sends 404 if no page is found */
if (!$endpoint) die(send(error_message(404)));

/**
 * @var String $procedure sets the global variable to catch the procedure to be executed
 */
$procedure;

/**
 * @var Array $body sets the body variable to catch body request params
 */
$body;

/**
 * @var Array $params sets the param variable to catch URL params
 */
$params;



/** Allows options request to check server */
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  send(['Allowed']);
  return;
}
if (is_array($endpoint)) {
  $endpoint[0]->render();
} else {
  $endpoint->render();
}
