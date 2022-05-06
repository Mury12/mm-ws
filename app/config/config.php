<?php

/**
 * This page is used to set configurations to the webservice such as the session
 * and router loader, page loader and DB Connection
 * Any variable set here is available globally, so be wise in your choices.
 * 
 * Less code is better.
 */

use Dotenv\Dotenv;
use MMWS\Factory\RequestFactory;
use MMWS\Handler\Router;
use MMWS\Handler\SESSION;
use MMWS\Middleware\Authentication;
use MMWS\Middleware\CACHE;
use MMWS\Middleware\Throttle;

/** Composer autoload */
require_once 'vendor/autoload.php';

/**
 * @var Dotenv\Dotenv $dotenv loads the environment variables in .env
 */
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

/** System defined variables */
require_once 'app/config/variables.php';

/** Autoloads all the classes */
require_once _DEFAULT_APPLICATION_PATH_ . '/autoload.php';

/** Database connection configuration file */
require_once 'app/config/db-conf.php';
/**
 * @var MMWS\Handler\Request contains the request data. If this is null, then the
 * request wasn't succeed.
 */
$request = RequestFactory::create();
/** instantiates the router */
$router = new Router();
/** init param array */
$param = array();
/** Prints the page headers */
$router->headers();
/** Init session */
SESSION::init();
/** Sets up Too many request middleware. 100 requests in 60 minutes max. Timeout 10 min */
$tmr = new Throttle(100, 60, 600);
$tmr->init();
/** Sets request caching interval */
CACHE::$timeout = 10;
/** Loads the Routes */
$routes = require_once(_DEFAULT_APPLICATION_PATH_ . '/router.php');
/** Creates the routes */
$router->init($routes);
/** Loads the page content (JSON ONLY) */
$endpoint  = $router->get();
/** Sets default middlewares that will be activated for every page */
$middlewares = [[new Authentication()]];
/**
 * @var Bool $caching gets if the endpoint is caching
 */
$caching = $endpoint->caching ?? $endpoint[0]->caching ?? false;
