# MM-WS - PHP Webservice Template @v0.10.0-beta

## Before using

Ensure that you have read this documentation until the end
before using this webservice. It contains very important
informations about the way it works.

Generate a SSH key at `./.ssh/ssh-name` in order to use JWT and set its name in `.env` file.

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

The `MMWS\Handler\Endpoint` component is very important. It is responsible for
every page/data rendering in the webservice, altough, it will need basically 2 functions:
> Note that is more effective to use `EndpointFactory::create()->{methodA}->{methodB}->..` to create endpoints.

```php
<?php 
  /**
   *  Loads the Endpoint Model
   */
  use MMWS\Handler\Endpoint;

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

```php
<?php 
use MMWS\Factory\EndpointFactory;

return [
  'route-get' => [
    'params' => ['param1', 'param2'] // Params to be put in the URL in its order route-name/param1/param2
    'body' => new EndpointFactory::create()
      ->get('band/led-zeppelin', 'getZoso')  // Function post|patch|put|get|delete uses specific request method and procedure. 
      ->post('band/yardbirds', 'dazedAndConfused') // Yes, you can use multiple methods.
      ->addMiddleware([new MiddlewareClassName()]) // Ads a middleware to do promise queue
      ->permission('auth') // But not mixed route permission
  ],

  'multi-route' => [
    'route-1' => [
      'params' => ['param1', 'param2'],
      'body' => EndpointFactory::create()
        ->post('page', 'method')
        ->cache()
    ],
    
    // You can now use the root URL with the `params` index
    'params' => ['param1'] // Allowed only one mid argument for a while
      'body' => EndpointFactory::create()
      ->get('page', 'method')
    ]
  ]
];

```
* Note that every param name you choose will be the same inside the function file. So, if you set param1 as a param, inside
the `yardbirds.php` file you'll just use `$param1`

## MVC Model

This project is MVC (actually Model-Controller-Entity) based using the following flow:

``` 
                                      ───────────── Handlers 
                                    /             /    \/    \
                                   / ── Controller -> Model -> Entity
             renders()            /               \ 
index.php(root) -> Webservice Page ── Middlewares   Factories
                                  \
                                    Services, Handlers, Factories

```

Preserve this sequence to better workflow and..

```php
/**
 * Use DocBlocks. 
 * As you see, documentation is more important than 
 * the project itself.
 */
```
---
## Must Know Functions

As any project, there are abstractions you should know before using it:

## Session

Use the static class `MMWS\Handler\SESSION` to handle PHP Sessions.

```php
<?php
/**
 * Loads the SESSION Class.
 */
use MMWS\Handler\SESSION;

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
---
## Headers

Its possible to easily change headers `content-type`, `http allowed methods`, `CORS` and `headers` simply
modifying the following global variables in `variables.php`:

```php
  /** Enable CORS to *EXACTLY* this URL */
  define('HTTP_CORS_URI', '*');

  /** Enable defined headers */
  define('HTTP_ALLOW_HEADERS', 'content-type, user-addr, authorization');

  /** Allow defined http methods to request */
  define('HTTP_ALLOW_METHODS', 'GET, POST, PATCH, PUT, DELETE, HEAD, OPTIONS');

  /** Sets the content type for the requests */
  define('HTTP_CONTENT_TYPE', 'application/json');

```
---
## Database Model Extractor

This module is responsible for creating the base files for working.
Ensure to use it to develop faster.

This extractor gets all the selected tables in a database and turns it 
in to 3 files: Model, Controler and Entity and they're all linked, so
you'll only need to use them. The `AbstractModel` and `AbstractController` classes
will do most of the job, letting you only with the Entity classes that you'll put
your CRUD business rules. Note that relation tables are possible but you will surely
need to adjust them in order to get the expected results.

To use it, just type `composer create-mvc` in your terminal.

Usage:

```php
<?php

use MMWS\Handler\DatabaseModelExtractor;

$gen = new DatabaseModelExtractor('database_name', 'mvc_path', 1, 'VENDOR', 'Prefix');
// It is now possible to set the tables to extract using the method below
$gen->setTables(['table_1', 'table_2']);
// If no table is set, it will get the whole database not including view tables.
$gen->generate();

```
Simply as that, all of the database tables (excluding view tables) will be in its folder models, entities and controllers
in the MVC path. The folders MUST ALREADY EXIST.
Note that you can of course count on `PDOQueryBuilder` class to build queries easily.

## The Query Builder

This template counts on simple query builder that counts on all the basic functions of a query.
It is auto-implemented when you use the db extractor, but you may also want to create your own
queries.

Note that when you use the method `AbstractModel::toArray`, this will turn the props into snake_case 
unless you specify that you dont want setting `$snake = false` on `MyModel::toArray([], false)`. Check 
the method description for further information.

Example of usage:
```php
$stmt = new PDOQueryBuilder('my_table');

// Insert John
$stmt->insert(['name' => 'jon']);
$result = $stmt->run();

// Select John
$stmt->select(['id as userId', 'name']);
$stmt->where('name', 'jon');
$result = $stmt->run();

$stmt->update(['email' => 'john@mail.com', 'name' => 'john'])
// If where is not set, it will throw an error
$stmt->where('id', 1);
$result = $stmt->run();

// Delete John
$stmt->delete();
// If where is not set, it will throw an error
$stmt->where('id', 1);
$result = $stmt->run();

// Raw query
$result = $stmt->raw('SELECT * FROM my_table WHERE id = ? OR name = ?', [1, 'john']);
```

---
## String Case Handler

You can convert snake_case to camelCase -- or CameCase -- and vice-versa

```php
$values = [
    'userName' => 'Garry',
    'userPassword' => 'M&UhanL2'
];

$str = MMWS\Handler\CaseHandler::convert($values, 1);
```

`$str` will result in:
```php
[
   'user_name' => 'Garry'
   'user_password' => 'M&UhanL2'
]
```
---
## Endpoints and Error Handling

Every HTTP error that you want to send to the client should use `RequestException` class just like the following example.

```php
<?php

global $request;

class Module extends View
{
    /**
     * Creates an user
     */
    function showMeAnError(): array
    {
       if($error){
          throw RequestExceptionFactory::create(
            ['error' => 'Cannot access this page', 'reason' => 'You are not allowed.'],
            403
          )
        }
    }
    function showMeAnotherError(): array
    {
       try{
          // something
        }catch(RequestExeption $ex)
          $ex->setMessage(['error' => 'Cannot access this page', 'reason' => 'You are not allowed.']);
          $ex->setCode(401);
          throw $ex;
        }
    }
}
return new View($request);
```
---
## General Functions

In `functions.php` you can see a lot of useful functions, such as password generators,
unique id generator, token generator, error handlers, etc., but the most used are:

 - `{$METHOD}_params()`: gets the POST|PATCH|PUT request body params. It's automatically done when the request comes and extracted to the page.
 - `send(Array $content)`: sends the response to the page. MUST BE array.
 - `http_message(Int $errCode)`: gets the corresponding http code error message you set.
 - `get_syserr(Int $errCode)`: gets the system errors you set in the json file. Useful for database returns or internal logging.
 - `perform_query_pdo(PDOStatement $q)`: Performs a query using PDO method. Note that you need to insert a prepared statement.
 - `make_array_from_query(PDOStatement $q, 'NAMESPACE\Class')`: Turns the above return into an array. Is possible to use classes, check the file.
 - `set_http_code(Int $code)`: sends an http code.
 - `report(Mixed $error)`: saves the error into a log in logs/error.log.

---
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
│   │   ├── local (Local hosting configuration files files)
│   │   ├── db-conf.php (DB Configuration file)
│   │   └── variables.php (CONST Variables definition file)
│   ├── \logs
│   │   └── You know what 'log' means, right?
│   ├── partials ── _core <-- Core files, don't touch :)
│   ├── partials ── classes
│   │   ├── \controller
│   │   │   └── Controller classes
│   │   ├── \entities
│   │   │   └── Database manipulation classes
│   │   ├── \factory
│   │   │   └── Factory classes
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
│   │   └── servoices.php (Webservice application services router)
│   ├── \sql
│   │   └── Put your sql files here
│   ├── \util (Utility files, templates, error definition, etc.)
│   └── System-messages.json (System messages definition used in get_sysmsg($errCode). Not HTTP errors.)
└── index.php (Endpoint renderer)
```
 ### Simple as that.
