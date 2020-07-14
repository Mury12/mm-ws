<?php

use MMWS\Model\Layout;

return [
    'getUniqId' => [
        'params' => ['len', 'hash'],
        'body' =>
        $l = new Layout,
        $l->page('uniqid_gen')
    ]
];
