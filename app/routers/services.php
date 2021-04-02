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

	'wine' => [
		'params' => ['manageId'],
		'body' => EndpointFactory::create()
			->post('manage/wine', 'create')
			->get('manage/wine', 'get')
			->put('manage/wine', 'update')
			->delete('manage/wine', 'delete'),
		// Add children routes calling the http methods from endpoint
		'another-children-route' => [
			'body' => EndpointFactory::create()
				->get('manage/wine', 'exampleMethod'),
		]
	],
];
