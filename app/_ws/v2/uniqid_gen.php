<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */

 /**
  * @var string $procedure gets the route function procedure
  */
global $procedure;
global $params;

/**
 * @var Array $procedures array of procedures to perform in the endpoint
 */
$procedures = array(
    'getUniqueId' => function ($d) {
        return unique_id($d['len'] ?? 6, $d['hash'] ?? 'sha256');
    }
);

if (array_key_exists($procedure, $procedures)) {
    /**
     * @var mixed $m result from the procedure
     */
    $m = $procedures[$procedure]($params ?? null);
    send(is_array($m) ? $m : ['res' => $m]);
} else {
    send(error_message(400));
}
return;

