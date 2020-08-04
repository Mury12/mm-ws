<?php

namespace MMWS\Interfaces;

require_once('app/util/ploader.php');

/**
 * Middleware abstract class
 */
interface Middleware
{
    function action();
    function init();
}