<?php

use MMWS\Handler\MMWS;

try {
  require 'app/config/config.php';
  // Instantiates the main class
  $mmws = new MMWS($_ENV['APP_ENV'] ?? 'development', 'index');
  // Runs the app
  $mmws->amaze();
} catch (Error $e) {
  header('content-type: application/json');
  die(send(
    http_message(
      $e->getCode(),
      json_decode($e->getMessage(), true)
    )
  ));
} catch (Exception $e) {
  header('content-type: application/json');
  die(send(
    http_message(
      $e->getCode(),
      json_decode($e->getMessage(), true)
    )
  ));
}
