<?php

namespace MMWS\Factory;

use MMWS\Handler\Request;

class RequestFactory {
    static function create() {
        return new Request();
    }
}