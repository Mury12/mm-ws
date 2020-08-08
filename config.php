<?php

/**
 * This page is used to set configurations to the webservice such as the session
 * and router loader, page loader and DB Connection
 * Any variable set here is available globally, so be wise in your choices.
 * 
 * ****** ONLY EDIT IF YOU KNOW WHAT YOU'RE DOING ******
 * 
 * Less code is better.
 */

use Dotenv\Dotenv;
use MMWS\Model\{
    SESSION,
    Router
};

/** Composer autoload */
require_once __DIR__.'/vendor/autoload.php';

/** Autoloads all the classes */
require_once 'app/autoload.php';

/** System defined variables */
require_once 'app/config/variables.php';

/** Database connection configuration file */
require_once 'app/config/db-conf.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/** instantiates the router */
$r = new Router();
/** init param array */
$param = array();
/** Prints the page headers */
$r->headers();
/** Init session */
SESSION::init();
/** Sets requests caching interval */
CACHE::$timeout = 10;
/** Loads the Routes */
$routes = require_once('app/routes.php');
/** Creates the routes */
$r->createRoutes($routes);
/** Loads the page content (JSON ONLY) */
$endpoint  = $r->getPage();

/**
 * @var Bool $caching gets the caching param
 */
$caching = $endpoint->caching ?? $endpoint[0]->caching ?? false;
