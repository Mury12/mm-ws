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
		'params' => ['codigo'],
		'body' => EndpointFactory::create()
			->post('wine/manage', 'create')
			->get('wine/manage', 'get')
			->put('wine/manage', 'update')
			->delete('wine/manage', 'delete')
			->cache(),
		// Add children routes calling the http methods from endpoint
		'another-children-route' => [
			'body' => EndpointFactory::create()
				->get('wine/manage', 'exampleMethod'),
		]
	],
	'manage' => [
		'params' => ['userId'],
		'body' => EndpointFactory::create()
			->post('user/manage', 'create')
			->get('user/manage', 'get')
			->put('user/manage', 'update')
			->delete('user/manage', 'delete'),
		// Add children routes calling the http methods from endpoint
		'another-children-route' => [
			'params' => ['id'],
			'body' => EndpointFactory::create()
				->post('user/manage', 'create')
				->get('user/manage', 'get')
				->put('user/manage', 'update')
				->delete('user/manage', 'delete'),
		]
	],
	'ws/v2' => [
		'params' => ['id'],
		'body' => EndpointFactory::create()
			->post('test/test', 'create')
			->get('test/test', 'get')
			->put('test/test', 'update')
			->delete('test/test', 'delete'),
		// Add children routes calling the http methods from endpoint
		'another-children-route' => [
			'body' => EndpointFactory::create()
				->get('test/test', 'exampleMethod'),
		]
	],
	'test' => [
		'params' => ['id'],
		'body' => EndpointFactory::create()
			->post('test/test', 'create')
			->get('test/test', 'get')
			->put('test/test', 'update')
			->delete('test/test', 'delete'),
		// Add children routes calling the http methods from endpoint
		'another-children-route' => [
			'body' => EndpointFactory::create()
				->get('test/test', 'exampleMethod'),
		]
	],
	'teste-aleatorio' => [
		'params' => ['id'],
		'body' => EndpointFactory::create()
			->post('test/test', 'create')
			->get('test/test', 'get')
			->put('test/test', 'update')
			->delete('test/test', 'delete'),
		// Add children routes calling the http methods from endpoint
		'another-children-route' => [
			'body' => EndpointFactory::create()
				->get('test/test', 'exampleMethod'),
		]
	],
];
