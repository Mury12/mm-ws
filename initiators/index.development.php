<?php

/**
 * USING MMWS v1.0.1-beta
 * @see https://github.com/mury12/mm-ws for more information
 * and updates.
 */

try {
  global $endpoint;
  /** Sends 404 if no page is found */
  if (!$endpoint) die(send(http_message(404)));

  /** Allows options request to check server */
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    send(http_message(204));
    return;
  }
  // Contains the response from the endpoint
  $response = $endpoint->render();
  // Sends it back to the client
  return send($response);
} catch (Exception $e) {
  throw $e;
} catch (Error $e) {
  throw $e;
}
