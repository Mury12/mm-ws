<?php

/**
 * Example of Usage
 * ```php
 * return [
 *  'user' => [
 *		'params' => ['id'], # it will be accessed at $this->data['params']['id'] in your Module file
 *		'body' => EndpointFactory::create()
 *			->post('user/manage', 'create')
 *			->get('user/manage', 'get')
 *			->put('user/manage', 'update')
 *			->delete('user/manage', 'delete'),
 *		// Add children routes calling the http methods from endpoint
 *		'another-children-route' => [
 *			'body' => EndpointFactory::create()
 *				->get('user/manage', 'exampleMethod'),
 *		]
 *	 ],
 * ]
 * ```
 */

use MMWS\Factory\EndpointFactory;
use MMWS\Middleware\Authentication;

return [
	'/' => [
		'params' => ['id'],
		'body' => EndpointFactory::create()
			->post('diet/manage', 'create', [
				'middlewares' => [
					[new Authentication()]
				]
			])
			->get('diet/manage', 'get')
			->put('diet/manage', 'update')
			->delete('diet/manage', 'delete'),
	],
	'meal' => [
		'params' => ['id'],
		'body' => EndpointFactory::create()
			->post('meal/manage', 'create')
			->get('meal/manage', 'get')
			->put('meal/manage', 'update')
			->delete('meal/manage', 'delete'),
	],
];
