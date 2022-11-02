<?php

namespace MMWS\Factory;

use MMWS\Interfaces\Factory;
use MMWS\Handler\Endpoint;

/**
 * Instantiates an endpoint
 * @return MMWS\Handler\Endpoint
 */
class EndpointFactory implements Factory {
    static function create(array $args = null) {
        return new Endpoint();
    }
}
