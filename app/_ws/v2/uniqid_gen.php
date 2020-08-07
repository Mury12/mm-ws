<?php

/**
 * Here comes the description for this endpoint
 * Ensure to do it, please.
 * 
 * *** DO NOT CHANGE THIS TEMPLATE IF IT'S ALREADY IN PRODUCTION ***
 */
global $procedure;
global $params;
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
    'session' => function () {
        return ['session' => 'oi'];
    }
);

if (array_key_exists($procedure, $procedures)) {

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
    send(error_message(400));
}
return;
