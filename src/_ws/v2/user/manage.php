<?php

/**
 * This is the Manage Module.
 * A Module is a class that extends a a View, performing as 
 * a controller to a certain endpoint. Use this class to
 * perform calls to the actual controllers that execute
 * functions related to this procedures.
 * 
 * Description of this endpoint
 *
 *
 */

use MMWS\Factory\RequestExceptionFactory;
use MMWS\Abstracts\View;
use MMWS\Controller\UserController;
use MMWS\Handler\JWTHandler;
use MMWS\Handler\RequestException;
use MMWS\Output\JwtResponse;

class Module extends View
{
    /**
     * Call the create method to create a new user into
     * the database.
     */
    function create(): array
    {
        $hasErrors = keys_match($this->body, ['name', 'email', 'password']);
        if (!$hasErrors) {
            $controller = new UserController($this->body);
            // Checks if the generated instance is the right user type
            $result = $controller->save();

            set_http_code(201);
            return $result;
        } else {
            throw RequestExceptionFactory::field($hasErrors);
        }
    }

    /**
     * Call the GET method to GET a single user or a set of users
     */
    function get()
    {
        $controller = new UserController($this->params);
        return $controller->get($this->query);
    }

    /**
     * Call the update method to update a single user
     * in the database
     */
    function update()
    {
        if (array_key_exists('id', $this->params)) {
            $controller = new UserController($this->body);
            $controller->model->id = $this->params['id'];
            return $controller->update();
        } else {
            throw RequestExceptionFactory::field(['id']);
        }
    }

    /**
     * Call the delete method to delete a single user in the database.
     */
    function delete()
    {
        if (array_key_exists('id', $this->params)) {
            $controller = new UserController($this->params);
            return $controller->delete();
        } else {
            throw RequestExceptionFactory::field(['id']);
        }
    }

    function login()
    {
        $hasErrors = keys_match($this->body, ['email', 'password']);
        if (!$hasErrors) {
            try {
                $controller = new UserController();
                $result = $controller->get([
                    'filters' => ['email' => $this->body['email']],
                ], true);

                if (!sizeof($result)) throw RequestExceptionFactory::create("Incorrect user or password", 401);

                $pwdMatch = $result[0]->matchPassword($this->body['password']);

                if ($pwdMatch) {
                    $jwt = JWTHandler::create($result[0]);
                    return new JwtResponse($jwt);
                } else {
                    throw RequestExceptionFactory::create("Incorrect user or password", 401);
                }
            } catch (RequestException $e) {
                throw $e;
            } catch (Error $e) {
                throw RequestExceptionFactory::create($e->getMessage(), $e->getCode());
            }
        } else {
            throw RequestExceptionFactory::field($hasErrors);
        }
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
