<?php

/**
 * This file contains all the defined constants for the system.
 * Do not change it before analysis
 */

// require_once('app/util/ploader.php');
if (!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'rb'));
if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if (!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));
/** Default core classes path */
define('_DEFAULT_CORE_PATH_', 'app/partials/_core');
/** Default module classes path */
define('_DEFAULT_MODULE_PATH_', 'app/partials/class');
/** Default application path */
define('_DEFAULT_APPLICATION_PATH_', _DEFAULT_CORE_PATH_ . '/application');
/** Default indexes file path */
define('_DEFAULT_STARTER_PATH_', _DEFAULT_CORE_PATH_ . "/application/starters");
if (!\file_exists('app/config/local/variables-local.php')) {

    /** * Route protector use flag (not quite done...) */
    define('_WILL_IT_SMITH_', true);
    /** Enabling debug mode will throw all errors to the server response */
    define('DEBUG_MODE', 1);
    /** * Global session save path */
    ini_set('session.save_path', realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));

    /**
     * Use the defined globals below to set the request default headers.
     * ---------------------------------
     * Enable CORS to *EXACTLY* this URL 
     */
    define('HTTP_CORS_URI', '*');

    /** Enable defined headers */
    define('HTTP_ALLOW_HEADERS', 'content-type, user-addr, authorization');

    /** Allow defined http methods to request */
    define('HTTP_ALLOW_METHODS', 'GET, POST, PATCH, PUT, DELETE, HEAD, OPTIONS');

    /** Sets the content type for the requests */
    define('HTTP_CONTENT_TYPE', 'application/json');


    /** Files */
    define('DEFAULT_FILE_PATH', 'app/_files/');

    /** MailGun */
    define('DEFAULT_NOREPLY_EMAIL', 'noreply@YOUR_EMAIL.com');
    define('DEFAULT_MAILGUN_URL', 'https://api.mailgun.net/v3/mg.YOUR_DOMAIN.com.br/messages');
    define('DEFAULT_MAILGUN_PWD', 'MG SMTP PWD');

    /** MailGun Keys */
    define('DEFAULT_MAILGUN_KEY', 'MG KEY');
    define('DEFAULT_MAILGUN_VALIDATION_KEY', 'MG PUBLIC KEY');
    define('DEFAULT_MAILGUN_WEBHOOK', 'MG WEBHOOK KEY');

    /** Consumer */

    // Enable the headers below if it will need permissioned pages

    // define('ORIGIN_HTTP_ADDR', getallheaders()['User-Addr'] ?? null);
    if (function_exists('getallheaders')) {
        define('USER_AUTHORIZATION_TOKEN', getallheaders()['Authorization'] ?? null);
    }
    // define('USER_AUTHORIZATION_TOKEN', 'AUTHORIZED');
    if (array_key_exists('JWT_KEY_PATH', $_ENV)) {
        $key = file_get_contents($_ENV['JWT_KEY_PATH']);
        define('_JWT_DEFINED_KEY_', $key);
    }
} else require_once 'app/config/local/variables-local.php';
