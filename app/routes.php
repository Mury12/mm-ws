<?php

/**
 * Routes are now defined separately in app/routers/router-file.php
 * Do not change this file to avoid route breaking.
 * This file is used only to define route domains such as ws->v1|v2|v3
 * and so on
 */

$v2 = require_once('app/routers/services.php');
$ms = require_once('app/routers/micro-services.php');
$errors = require_once('app/routers/errors.php');

$v2['ms'] = $ms;

return [
    'ws' => [
        'v2' => $v2,
    ],
    'error' => $errors
];
