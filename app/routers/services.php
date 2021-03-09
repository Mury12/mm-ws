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

use MMWS\Model\Endpoint;

return [
    'login' => [
        'body' => $e = new Endpoint(),
        $e->post('user/login', 'auth_request')
            ->permission('any')
    ],

    'signup' => [
        'body' => $e = new Endpoint,
        $e->get('user/signup', 'create_user')
            ->permission('not')
    ],
    'company' => [
        'assign' => [
            'params' => ['processId', 'processName'],
            'body' => $e = new Endpoint(),
            $e->get('user/login', 'shown')
        ],
        'params' => ['companyId'],
        'body' => $e = new Endpoint(),
        $e->get('user/login', 'shown')
    ],

];
