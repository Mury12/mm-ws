# PHP Webservice

## Before using

Ensure that you have read this documentation until the end
before using this webservice. It contains very important
informations about the way it works.

## Running

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

The `MMWS\Model\Layout component` is very important. It is responsible for
every page/data rendering in the webservice, altough, it will need basically 2 functions:

``` 
<?php 
  /**
   *  Loads the Layout Model
   */
  use MMWS\Model\Layout;

  /**
   * Instantiates the layout.
   */
  $l = new Layout();

  /** 
   * Domain as the folder inside _ws/v2/domain and page is the actual php inside this folder 
   * such as domain/login.php. Yes, you dont need ".php" extension in this parameter.
   */
  $l->page('doimain/page');

  /**
   * When permission is called, the auth middleware makes a verification for authorization.
   */
  $l->permission('auth');
```

Of course there are other controls but its not needed. If you want to know more, check Layout model file. Fully documented.
Just enjoy this following example about how to create a route:

``` 
<?php 
use MMWS\Model\Layout

return [
  'route-name' => [
    'params' => ['param1', 'param2'] // Params to be put in the URL in its order route-name/param1/param2
    'body' => $jimmy = new Layout(),
      $jimmy->page('band/yardbirds')
      ->permission('auth')
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
 * Use DockBlox. 
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

### General Functions

In `functions.php` you can see a lot of useful functions, such as password generators,
unique id generator, token generator, error handlers, etc., but the most used are:

 - `get_post()`: gets the POST request.
 - `send(Array $content)`: sends the response to the page. MUST BE array.
 - `error_message(Int $errCode)`: gets the corresponding http code error message you set.
 - `get_syserr(Int $errCode)`: gets the system errors you set in the json file. Useful for database returns or internal logging.
 - `perform_query_pdo(PDOStatement $q)`: Performs a query using PDO method. Note that you need to insert a prepared statement.
 - `make_array_from_query(PDOStatement $q)`: Turns the above return into an array. Is possible to use classes, check the file.
 - `set_error_code(Int $code)`: sends an http error.

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
│   ├── autoload.php (Autoloads all files included in partials/classes. Not the composer autoload.)
│   ├── config.php (Loads the global settings)
│   ├── routes.php (Route handler)
│   ├── functions.php (Global functions)
│   └── System-messages.json (System messages definition used in get_sysmg($errCode). Not HTTP errors.)
└── index.php (Layout renderer)
```
 ### Thats all folks.
