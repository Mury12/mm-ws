<?php

/**
 * This is a Local system variables configuration file. 
 * Rename this file to variables-local.php if you want
 * to use some local custom variables
 * and do not remove this file from .gitignore
 */

/**
 * Global session save path
 */
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
define('DEFAULT_FILE_PATH', 'src/upload/');

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
define('USER_AUTHORIZATION_TOKEN', getallheaders()['Authorization'] ?? null);
// define('USER_AUTHORIZATION_TOKEN', 'AUTHORIZED');
$key = file_get_contents($_ENV['JWT_KEY_PATH']);
define('_JWT_DEFINED_KEY_', $key);
