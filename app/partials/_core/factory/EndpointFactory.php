<?php

namespace MMWS\Factory;

use MMWS\Interfaces\IFactory;
use MMWS\Handler\Endpoint;

/**
 * Instantiates an endpoint
 * @return MMWS\Handler\Endpoint
 */
class EndpointFactory implements IFactory {
    static function create(array $args = null) {
        return new Endpoint();
    }
}