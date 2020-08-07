<?php

use MMWS\Model\Endpoint;

return [
    'getUniqId' => [
        'uid' => [
            'params' => ['len', 'hash'],
            'body' =>
            $e = new Endpoint(),
            $e->get('uniqid_gen', 'getUniqueId')
        ],
        'session' => [
            'params' => ['user'],
            'body' => 
            $e = new Endpoint(),
            $e->get('uniqid_gen', 'session')
            ->addMiddleware(array([new Cache()]))
        ]
    ]
];
