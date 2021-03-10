<?php

use MMWS\Model\Endpoint;

return [
    'unique-id' => [
            'params' => ['len', 'hash'],
            'body' =>
            $e = new Endpoint(),
            $e->get('uniqid-gen', 'getUniqueId')
                ->cache()
    ],
];
