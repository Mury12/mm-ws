<?php

namespace MMWS\Factory;

use MMWS\Interfaces\IFactory;
use MMWS\Model\Endpoint;

/**
 * Instantiates an endpoint
 * @return MMWS\Model\Endpoint
 */
class EndpointFactory implements IFactory {
    static function create(array $args = null) {
        return new Endpoint();
    }
}