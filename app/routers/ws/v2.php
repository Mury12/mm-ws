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

return [

];
