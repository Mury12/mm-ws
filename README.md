# MM-WS - PHP Webservice Template @v0.12.1-beta1 (Breaking changes!)

__V0.12.x is is migrating to PHP 8.x, and older versions will be marked as stale.__

> V0.12.1-beta0 does not support backwards compat with v0.11.x or lower.

## Before using

Ensure that you have read this documentation until the end
before using this webservice. It contains very important
informations about the way it works.

Generate a SSH key at `./.ssh/ssh-name` in order to use JWT and set its name in `.env` file.

## Running

`composer install` - to install dependencies

Webservice can be initialized with PHP CLI or any bundler you want.
`php -S localhost:8081`

## Initiators

Initiators are index files used to initiate the server. If you need a more complex index file you can set up
at `index.php` changing the default index file. Note that you'll need to cretate two files for it: one for
development and another for production so you'll have:

1. `/initiators/my-index-file.production.php`
2. `/initiators/my-index-file.development.php`

And `index.php` will look like:

```php
<?php

use MMWS\Handler\MMWS;
// Load configurations
require 'src/config/config.php';
// Instantiates the main class
$mmws = new MMWS('my-index-file');
// Runs the app
$mmws->amaze();

?>
```

The `my-index-file.env.php` will look like:

```php
try {
  /*------------------Necessary code-------------------*/
  global $endpoint;
  /** Sends 404 if no page is found */
  if (!$endpoint) die(send(http_message(404)));
  /** Allows options request to check server */
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    send(http_message(204));
    return;
  }
  // Contains the response from the endpoint
  $response = $endpoint->render();
  /*--------------------------------------------------*/

  # Here and in any other empty space you can put
  # any kind of code but this is the necessary code.

  /*------------------Necessary code-------------------*/
  // Sends it back to the client
  return send($response);
  /*--------------------------------------------------*/
} catch (Exception $e) {
  throw $e;
} catch (Error $e) {
  throw $e;
}
```

## Routes

Routes are added to its specific file inside `src/routers/ROUTE-GROUP-NAME.php`
If another router file is needed, also is needed to modify `src/routes.php` in order
to add a new domain to this file.

### Route model:

- `src/routers/ws/v2.php` -> The actual webservices routes
- `src/routers/ms.php` -> General service routes
- `src/routers/error.php` -> Error routes

> Note that you can add as much router files as you want. The name of the file will be the prefix
> so if you need to create multiple domains, it is possible to use as folders, just like 
> `src/routers/ws/my-subpath/my-sub-subpath/my-context.php` will be translated to
> `ws/my-subpath/my-sub-subpath/my-context/:route`.

> Also note that module-create will ask you for the default route file or it will be created inside
> `routers/ws/v2.php`. If you want to write into another file, type the path like `ws/v1/domain.php` and
> it will search for the file. It must exist before try.

### Example

`src/routers/ws/v2.php` :

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

> Note that `EndpointFactory::create()` is an abstraction for `[$e = new Layout(), $e->fn()->..]`.
> Just enjoy this following example about how to create a route:

```php
<?php
use MMWS\Factory\EndpointFactory;

return [
  'route-get' => [
    'params' => ['param1', 'param2'] // Params to be put in the URL in its order route-name/param1/param2
    'body' => new EndpointFactory::create()
      ->get('band/led-zeppelin', 'getZoso')  // Function post|patch|put|get|delete uses specific request method and procedure.
      ->post('band/yardbirds', 'dazedAndConfused', [// Yes, you can use multiple methods.
        'middlewares' => [
          // adds middleware to a specific method
          [new MiddlewareClassName, 'initFunctionName'] // `initFunctionName` is default `init`. If you implement Middleware interface, no need to set.
        ]
      ])
      ->addMiddleware([[new MiddlewareClassName(), 'initFunctionName']]) // Ads a middleware to do promise queue. This will act in the whole subpath
  ],

  'multi-route' => [
    'route-1' => [
      'params' => ['param1', 'param2'],
      'body' => EndpointFactory::create()
        ->post('page', 'method')
        ->cache() // In-memory Caches the result for a certain amount of time. This will repeat the result to that session until the time set in config.php is passed.
    ],

    // You can now use the root URL with the `params` index
    'params' => ['param1'] // Allowed only one mid argument for a while
      'body' => EndpointFactory::create()
      ->get('page', 'method')
    ]
  ]
];

```

- Note that every param name you choose will be the same inside the function file. So, if you set param1 as a param, inside
  the procedure at `_ws/v2/band/yardbirds.php` file you'll get it by using `$this->data['params']['param1']`

## Request Model

The request model of our webservice is simple to use. Basically, it is composed by:

1.  The Request object;
2.  `Request::body`;
3.  `Request::params`; and
4.  `Request::query`.

The `Module` class used to build an endpoint extends the `View` class, that has a `Request` object inside. So, to
access these properties, you'll use `$this->data['prop']` as following:

```php

class Module extends View {
  function myFunction() {
    // Get the request query params
    $query = $this->data['query'];
    // Get the request path params
    $params = $this->data['params'];
    // Get the request body (json)
    $body = $this->data['body'];
    // instantiates something
    $ctl = new MyController($body, $params, $query);
    // Return results to our sender
    return $ctl->myFunction();
  }
}
global $request;
return new Module($request);

```

### Response

After the method is called, the server will produce a response exactly as the return of the controller's result.

> Note that if the response is not an array, it will be turned into an array like `[0: "my individual result"]` to be
> returned as a JSON object, and if it is an array that may be converted to a JSON object, it will be as follows:

```json
// GET user/10
{
  "id": 10,
  "name": "Jon",
  "email": "jon@example.com"
}
```

If the server encounters an error, a default structure will be sent to the client:

```json
{
  "message": "Bad request",
  "code": 400,
  "at": 1623624387
}
```

If any detail extends the error, it will come in a different property as `status`:

```json
{
  "message": "Bad request",
  "code": 400,
  "at": 1623624506,
  "status": {
    "error": "Some fields are missing",
    "fields": ["code"]
  }
}
```

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

## Must Know

As any project, there are abstractions you should know before using it:

### Session

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

### Headers

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

## The Database Model Extractor

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

use MMWS\Handler\DBPuller;

$gen = new DBPuller('database_name', 'mvc_path', 1, 'VENDOR', 'Prefix');
// It is now possible to set the tables to extract using the method below
$gen->setTables(['table_1', 'table_2']);
// If no table is set, it will get the whole database not including view tables.
$gen->generate();

```

Simply as that, all of the database tables (excluding view tables) will be in its folder models, entities and controllers
in the MVC path. The folders MUST ALREADY EXIST.
Note that you can of course count on `PDOQueryBuilder` class to build queries easily.

## The Module Self-Creator

It is possible to create endpoints automatically. All you need is to type `composer create-module groupname domain`.
So, if you want to create an endpoint to manage users, just type `composer create-module manage user` and look to
the terminal and answer the required questions to proceed.

After the creation is finished, you'll need to go to `_ws/v2/domain/groupname.php` and adjust the required functions.
As it is a basic generic generator, it will not write business rules for you.

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

You can convert snake*case to \_camelCase* or _PascalCase_ and vice-versa

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
├── core <-- Core files, don't touch :)
├── \app
│   ├── \upload
│   │   ├── File A.jpg
│   │   ├── \Folder B
│   │   │   └── File B.pdf
│   │   └── \Folder C
│   ├── \_ws
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
│   ├── classes
│   │   ├── \Controller
│   │   │   └── Controller classes
│   │   ├── \Entity
│   │   │   └── Database manipulation classes
│   │   ├── \Factory
│   │   │   └── Factory classes
│   │   ├── \Handler
│   │   │   └── File handlers, exception, whatever
│   │   ├── \Interfaces
│   │   │   └── Interface classes
│   │   ├── \Middleware
│   │   │   └── Middleware classes
│   │   ├── \Model
│   │   │   └── Model classes
│   │   └── \Services
│   │       └── Services classes
│   │   └── \Traits
│   │       └── Traits
│   ├── \routers <- router files
│   │   ├── error.php (Error router file)
│   │   ├── ms.php (General services router)
│   │   └── ws/v2.php (Webservice application services router)
│   ├── \sql
│   │   └── Put your sql files here
│   ├── \util (Utility files, templates, error definition, etc.)
│   └── System-messages.json (System messages definition used in get_sysmsg($errCode). Not HTTP errors.)
├── \initiators
│   ├── index.production.php
│   └── index.development.php
└── index.php (Endpoint renderer)
```

### It is simple, don't you think?
