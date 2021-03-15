<?php

use MMWS\Factory\EndpointFactory;

return [
    'unique-id' => [
        'params' => ['len', 'hash'],
        'body' => $e = EndpointFactory::create()
            ->get('uniqid-gen', 'getUniqueId')
            ->cache()
    ],
];
