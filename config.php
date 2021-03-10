<?php

/**
 * This page is used to set configurations to the webservice such as the session
 * and router loader, page loader and DB Connection
 * Any variable set here is available globally, so be wise in your choices.
 * 
 * Less code is better.
 */

use Dotenv\Dotenv;
use MMWS\Handler\Router;
use MMWS\Handler\SESSION;
use MMWS\Middleware\CACHE;

/** Composer autoload */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * @var Dotenv\Dotenv $dotenv loads the environment variables in .env
 */
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/** Autoloads all the classes */
require_once 'app/autoload.php';

/** System defined variables */
require_once 'app/config/variables.php';

/** Database connection configuration file */
require_once 'app/config/db-conf.php';


/** instantiates the router */
$router = new Router();
/** init param array */
$param = array();
/** Prints the page headers */
$router->headers();
/** Init session */
SESSION::init();
SESSION::loadCookies();
/** Sets request caching interval */
CACHE::$timeout = 10;
/** Loads the Routes */
$routes = require_once('app/routes.php');
/** Creates the routes */
$router->init($routes);
/** Loads the page content (JSON ONLY) */
$endpoint  = $router->get();

/**
 * @var Bool $caching gets if the endpoint is caching
 */
$caching = $endpoint->caching ?? $endpoint[0]->caching ?? false;
