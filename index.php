<?php

require_once 'app/config.php';

use MMWS\Handler\Queue;
use MMWS\Middleware\Authentication;

$procedure;
$body;
$params;

/** Allows options request to check server */
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  send(['Allowed']);
  return;
} else {

  /**
   * @var Queue $middleware MMWS\Interfaces\Middleware queue to be executed AFTER the page rendering
   */
  $middleware = new Queue(
    'MMWS\Interfaces\Middleware',
    array_merge(
      array(
        [new Authentication()],
      ),
      $endpoint->middlewares
    )
  );
  $middleware->init();
  if ($middleware->Authentication) {
    $endpoint->render();
  }
}

$endpoint->render();
