<?php

/**
 * This file contains all the defined constants for the system.
 * Do not change it before analysis
 */

 require_once('app/util/ploader.php');

if (!\file_exists('./variables-local.php')) {
    ini_set('session.save_path', realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../session'));

    define('_WILL_IT_SMITH_', true);

    /** Enable CORS to *EXACTLY* this URL */
    define('HTTP_CORS_URI', '*');

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

    // Enable the headers below if needs permissioned page
    
    # define('ORIGIN_HTTP_ADDR', getallheaders()['User-Addr'] ?? null);
    // define('USER_AUTHORIZATION_TOKEN', getallheaders()['Authorization'] ?? null);
    define('USER_AUTHORIZATION_TOKEN', 'AUTHORIZED');
} else require_once './variables-local.php';
