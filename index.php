<?php

require_once 'app/config.php';

use MMWS\Middleware\Authentication;

/** Allows options request to check server */
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  send(['Allowed']);
  return;
} else {
  /** Check for a valid IP. Could be better at checking. */
  // if (!preg_match('/(\d){1,3}\.(\d){1,3}\.(\d){1,3}\.(\d){1,3}/', ORIGIN_HTTP_ADDR)) {
  //   send(error_message(401));
  //   die();
  // }
  /** Requires the authentication middleware */
  $permit = new Authentication();
  if ($permit) $layout->render();
}

$layout->render();
