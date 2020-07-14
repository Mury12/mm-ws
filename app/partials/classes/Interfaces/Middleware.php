<?php

namespace MMWS\Interfaces;

require_once('app/util/ploader.php');

interface Middleware
{
    function action();
    function init();
}