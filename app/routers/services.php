<?php

/**
 * Routes are now defined outside main router file.
 * Now its possible to add params in the current layout
 * index, such as:
 *  'route-name' => [
 *      'params' => ['param1', 'param2'],
 *      'body'   => $l = new Layout(),
 *                  $l->page('domain/page')
 *                    ->permission('auth')
 *  ],
 * 
 * Params index MUST BE before body properties, otherwise, it will be lost.
 */

use MMWS\Model\Layout;

return [
    'login' => [
        'body' => $l = new Layout(),
        $l->page('user/login')
            ->permission('any')
    ],

    'signup' => [
        'body' => $l = new Layout,
        $l->page('user/signup')
            ->permission('not')
    ]
];
