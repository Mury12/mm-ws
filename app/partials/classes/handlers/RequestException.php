<?php

namespace MMWS\Handler;

use Exception;

class RequestException extends Exception {
    protected $code = 500;
}