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

/**
 * @var Array $procedures array of procedures to perform in the endpoint
 */
$procedures = array(
    'procedure_name' => function ($d) {
    }
);

if (array_key_exists($procedure, $procedures)) {
    /**
     * @var mixed $m result from the procedure
     */
    $m = $procedures[$procedure]($data);
    send(is_array($m) ? $m : ['res' => $m]);
} else {
    send(error_message(400));
}
