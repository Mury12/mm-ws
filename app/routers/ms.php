<?php

use MMWS\Factory\EndpointFactory;

return [
    'unique-id' => [
        'params' => ['len', 'hash'],
        'body' => $e = EndpointFactory::create()
            ->get('uniqid-gen', 'getUniqueId')
            ->cache()
    ],
	'teste' => [
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
