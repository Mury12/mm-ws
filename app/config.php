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

use MMWS\Model\{
    SESSION,
    Router
};

/** Autoloads all the classes */
require_once 'autoload.php';

/** System defined variables */
require_once 'config/variables.php';

/** Database connection configuration file */
require_once 'config/db-conf.php';


/** instantiates the router */
$r = new Router();
/** Prints the page headers */
$r->headers();
/** Init session */
SESSION::init();
/** Loads the Routes */
$routes = require_once('routes.php');
/** Creates the routes */
$r->createRoutes($routes);
/** Loads the page content (JSON ONLY) */
$layout  = $r->getPage();

