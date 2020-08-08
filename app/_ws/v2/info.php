<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */


/**
 * @var String $procedure the global variable to catch the procedure to be executed
 */
global $procedure;

/**
 * @var Array $body the body variable to catch body request params
 */
global $body;

/**
 * @var Array $params the param variable to catch URL params
 */
global $params;

/**
 * @var Bool $caching check if this endpoints caches requests
 */
global $caching;


/**
 * @var Array $procedures array of procedures to perform in the endpoint
 */
$procedures = array(
    'version' => function ($d) {
        return $_ENV['APP_NAME'] . '^' . $_ENV['APP_VERSION'];
    },

);

if (array_key_exists($procedure, $procedures)) {

    /** Check if this endpoit is caching requests */
    if ($caching) {
        $cached = CACHE::check($procedure);
        /**
         * CACHEs requests if caching is enabled
         */
        if (!$cached) {
            /**
             * @var mixed $m result from the procedure
             */
            $m = $procedures[$procedure]($params ?? null);
            CACHE::put($m, $procedure);
        }

        $m = $m ?? $cached;
    } else {
        $m = $procedures[$procedure]($params ?? null);
    }
    send(is_array($m) ? $m : ['res' => $m]);
} else {
    send(http_message(400));
}
return;
