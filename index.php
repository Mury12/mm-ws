<?php

use MMWF\Factory\RequestFactory;

try {
  /** Loads config.php */
  require_once 'config.php';

  /** Sends 404 if no page is found */
  if (!$endpoint) die(send(http_message(404)));

  /**
   * @var MMWS\Handler\Request contains the request data. If this is null, then the
   * request wasn't succeed.
   */
  $request = RequestFactory::create();

  /** Allows options request to check server */
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    send(http_message(204));
    return;
  }

  if (is_array($endpoint)) {
    $endpoint[0]->render();
  } else {
    $endpoint->render();
  }
} catch (Exception $e) {
  require './app/functions.php';
  set_http_code(500);
  header('content-type: application/json');
  die(send(
    http_message(
      500,
      $e->getMessage()
    )
  ));
}
