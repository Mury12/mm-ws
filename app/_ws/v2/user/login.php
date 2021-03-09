<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */


/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
/**
 * @var Bool $caching check if this endpoints caches requests
 */
global $caching;




/**
 * @var Array $procedures array of procedures to perform in the endpoint
 */
$procedures = array(
    'getUniqueId' => function ($d) {
        return unique_id($d['len'] ?? 6, $d['hash'] ?? 'sha256');
    },
    'sayMyName' => function ($d) {
        return ['msg' => 'My name'];
    },
    'shown' => function ($d) {
        return $d;
    }
);

if (array_key_exists($request->getProcedure(), $procedures)) {

    /** Check if this endpoit is caching requests */
    if ($caching) {
        $cached = CACHE::check($request->getProcedure());
        /**
         * CACHEs requests if caching is enabled
         */
        if (!$cached) {
            /**
             * @var mixed $m result from the procedure
             */
            $m = $procedures[$request->getProcedure()]($request->data() ?? null);
            CACHE::put($m, $request->getProcedure());
        }

        $m = $m ?? $cached;
    } else {
        $m = $procedures[$request->getProcedure()]($request->data() ?? null);
    }
    send(is_array($m) ? $m : ['res' => $m]);
} else {
    send(http_message(400));
}
return;
