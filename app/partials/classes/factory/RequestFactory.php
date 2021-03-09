<?php

namespace MMWF\Factory;

use MMWS\Handler\Request;

class RequestFactory {
    static function create() {
        return new Request();
    }
}