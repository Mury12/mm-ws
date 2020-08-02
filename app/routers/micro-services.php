<?php

use MMWS\Model\Endpoint;

return [
    'getUniqId' => [
        'params' => ['len', 'hash'],
        'body' =>
        $e = new Endpoint(),
        $e->get('uniqid_gen', 'getUniqueId')
    ]
];
