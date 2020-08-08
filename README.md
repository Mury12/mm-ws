# PHP Webservice

## Before using

Ensure that you have read this documentation until the end
before using this webservice. It contains very important
informations about the way it works.

## Running

`composer install` - to install dependencies

Webservice can be initialized with PHP CLI or any bundler you want.
`php -S localhost:8081` 

## Routes

Routes are added to its specific file inside `app/routers/ROUTE-GROUP-NAME.php` 
If another router file is needed, also is needed to modify `app/routes.php` in order
to add a new domain to this file.

### Route model:

 - `app/routers/services.php` -> The actual webservices routes
 - `app/routers/micro-services.php` -> General service routes
 - `app/routers/errors.php` -> Error routes

### Example

`app/routers/services.php` :

The `MMWS\Model\Endpoint component` is very important. It is responsible for
every page/data rendering in the webservice, altough, it will need basically 2 functions:

``` 
<?php 
  /**
   *  Loads the Endpoint Model
   */
  use MMWS\Model\Endpoint;

  /**
   * Instantiates the layout.
   */
  $e = new Endpoint();

  /** 
   * Domain as the folder inside _ws/v2/domain and page is the actual php inside this folder 
   * such as domain/login.php. Yes, you dont need ".php" extension in this parameter.
   */
  $e->get('doimain/page', 'procedure');

  /**
   * When permission is called, the auth middleware makes a verification for authorization.
   */
  $e->permission('auth');

  /**
   * Cache requests in this route. Default timeout to a new request is 10 seconds. Check `config.php`
   */
   $e->cache();
```

Of course there are other controls but its not needed. If you want to know more, check Endpoint model file. Fully documented.
Just enjoy this following example about how to create a route:

``` 
<?php 
use MMWS\Model\Endpoint

return [
  'route-get' => [
    'params' => ['param1', 'param2'] // Params to be put in the URL in its order route-name/param1/param2
    'body' => $jimmy = new Endpoint(),
      $jimmy->get('band/led-zeppelin', 'getZoso')  // Function post|patch|put|get|delete uses specific request method and procedure. 
      ->post('band/yardbirds', 'dazedAndConfused') // Yes, you can use multiple methods.
      ->addMiddleware([new MiddlewareClassName()]) // Ads a middleware to do promise queue
      ->permission('auth') // But not mixed route permission
  ],

  'multi-route' => [
    'route-1' => [
      'params' => [],
      'body' => [
        $e = new Endpoint(),
        $e->post('page', 'method')
        ->cache()
      ]
    ],
    
    // You can also use the root URL without the `params` index
    'body' => [
      $e = new Endpoint(),
      $e->get('page', 'method')
    ]
  ]
];

```
* Note that every param name you choose will be the same inside the function file. So, if you set param1 as a param, inside
the `yardbirds.php` file you'll just use `$param1`

## MVC Model

This project is MVC based using the following flow:

``` 
                                      ───────────── Handlers 
                                    /             /    \/    \
                                   / ── Controller -> Model -> Entity
             renders()            /       
index.php(root) -> Webservice Page ── Middlewares
                                  \
                                    Services, Handlers

```

Preserve this sequence to better workflow and..

``` 
/**
 * Use DocBlocks. 
 * As you see, documentation is more important than 
 * the project itself.
 */
```

## Must Know Functions

As any project, there are abstractions you should know before using it:

### Session

Use the static class `MMWS\Model\SESSION` to handle PHP Sessions.

```
<?php
/**
 * Loads the SESSION Class.
 */
use MMWS\Model\SESSION;

/**
 * Starts the session.
 */
SESSION::init();

/**
 * Gets the before stored session.
 */
SESSION::get('sessionName');

/**
 *  Saves an item to the session
 */ 
SESSION::add('sessionName', 'value');

/**
 *  Destroys the session
 */ 
SESSION::done();

```

### Headers

Its possible to easily change headers `content-type`, `http allowed methods`, `CORS` and `headers` simply
modifying the following global variables in `variables.php`:

```
  /** Enable CORS to *EXACTLY* this URL */
  define('HTTP_CORS_URI', '*');

  /** Enable defined headers */
  define('HTTP_ALLOW_HEADERS', 'content-type, user-addr, authorization');

  /** Allow defined http methods to request */
  define('HTTP_ALLOW_METHODS', 'GET, POST, PATCH, PUT, DELETE, HEAD, OPTIONS');

  /** Sets the content type for the requests */
  define('HTTP_CONTENT_TYPE', 'application/json');

```

### Database Model Extractor

No more worrying in creating the MVC files. In this environment, you have the
Conflex Model Database Model Extractor. But well.. What does it mean??

In every project, you MUST HAVE to build your database first, right? So
Once you've done it, you've already done the MVC files. Well, Its not an ORM,
because I hate them but it converts every table in your remote database to 
a Controller, a Model and an Entity so you only worry to program it.

Maybe in future the CRUD functions are added but for now, only method
definition and no function. To do this, you can run it separately
with like `php debug.php`.

Usage:

```
<?php

use MMWS\Handler\ConflexModelExtractor;

$gen = new ConflexModelExtractor('database_name', 'mvc_path', 1, 'VENDOR', 'Prefix');
$gen->generate();

```
Simply as that, all of the database tables (excluding view tables) will be in its folder models, entities and controllers
in the MVC path. The folders MUST ALREADY EXISTS.

### General Functions

In `functions.php` you can see a lot of useful functions, such as password generators,
unique id generator, token generator, error handlers, etc., but the most used are:

 - `get_{$METHOD}()`: gets the POST|PATCH|PUT request body params. It's automatically done when the request comes and extracted to the page.
 - `send(Array $content)`: sends the response to the page. MUST BE array.
 - `http_message(Int $errCode)`: gets the corresponding http code error message you set.
 - `get_syserr(Int $errCode)`: gets the system errors you set in the json file. Useful for database returns or internal logging.
 - `perform_query_pdo(PDOStatement $q)`: Performs a query using PDO method. Note that you need to insert a prepared statement.
 - `make_array_from_query(PDOStatement $q, 'NAMESPACE\Class')`: Turns the above return into an array. Is possible to use classes, check the file.
 - `set_http_code(Int $code)`: sends an http code.
 - `report(Mixed $error)`: saves the error into a log in logs/error.log.

## Directory Tree

``` 
├── \app
│   ├── \_files
│   │   ├── File A.jpg
│   │   ├── \Folder B
│   │   │   └── File B.pdf
│   │   └── \Folder C
│   ├── \_ws_
│   │   └── \v2
│   │       ├── \error (Error feedback files if wanted) 
│   │       ├── \domain A
│   │       │    └── endpoint-for-domain-a.php (Each domain can have any amount of endpoints)
│   │       ├── \band
│   │       │    └── yardbirds.php
│   │       └── anyservice.php
│   ├── \config
│   │   ├── local (Local hosting files)
│   │   ├── db-conf.php (DB Configuration file)
│   │   └── variables.php (CONST Variables definition file)
│   ├── \logs
│   │   └── You know what 'log' means, right?
│   ├── partials ── classes
│   │   ├── \controller
│   │   │   └── Controller classes
│   │   ├── \entities
│   │   │   └── Database manipulation classes
│   │   ├── \handlers
│   │   │   └── File handlers, exception, whatever
│   │   ├── \interfaces
│   │   │   └── Interface classes
│   │   ├── \middleware
│   │   │   └── Middleware classes
│   │   ├── \model
│   │   │   └── Model classes
│   │   └── \services 
│   │       └── Services classes
│   ├── \routers
│   │   ├── errors.php (Error router file)
│   │   ├── micro-services.php (General services router)
│   │   └── variables.php (Webservice application services router)
│   ├── \sql
│   │   └── Put your sql files here
│   ├── \util (Utilities files, templates, error definition, etc.)
│   ├── autoload.php (Autoloads all files included in partials/classes. THIS IS NOT the composer autoload.)
│   ├── config.php (Loads the global settings)
│   ├── routes.php (Root routes definition)
│   ├── functions.php (Global functions)
│   └── System-messages.json (System messages definition used in get_sysmsg($errCode). Not HTTP errors.)
└── index.php (Endpoint renderer)
```
 ### Thats all folks.
