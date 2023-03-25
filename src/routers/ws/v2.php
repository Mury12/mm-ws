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
	'users' => [
		'params' => ['id'],
		'body' => EndpointFactory::create()
			->post('user/manage', 'create')
			->get('user/manage', 'get', [
				'middlewares' => [[new Authentication()]]
			])
			->put('user/manage', 'update', [
				'middlewares' => [[new Authentication()]]
			])
			->delete('user/manage', 'delete', [
				'middlewares' => [[new Authentication()]]
			]),
		'login' => [
			'body' => EndpointFactory::create()
				->post('user/manage', 'login'),
		]
	],
];
