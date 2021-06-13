<?php

use MMWS\Factory\EndpointFactory;

return [
    '/' => [
        'body' => EndpointFactory::create()
            ->get('amaze', 'me')
    ],
    'error' => [
        'params' => ['code'],
        'body' => $e = EndpointFactory::create()
            ->get('amaze', 'errors')
            ->cache()
    ],
];
