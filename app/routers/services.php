<?php

/**
 * Routes are now defined outside main router file.
 * Now its possible to add params in the current layout
 * index, such as:
 *  'route-name' => [
 *      'params' => ['param1', 'param2'],
 *      'body'   => $l = new Endpoint(),
 *                  $l->page('domain/page')
 *                    ->permission('auth')
 *  ],
 * 
 * Params index MUST BE before body properties, otherwise, it will be lost.
 */

use MMWS\Factory\EndpointFactory;

return [
    'login' => [
        'body' => EndpointFactory::create()
            ->post('user/login', 'login')
            ->permission('any')
    ],
    'signup' => [
        'body' => EndpointFactory::create()
            ->post('user/signup', 'signup')
            ->permission('not')
    ],
];
