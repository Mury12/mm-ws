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
use MMWS\Interfaces\View;
use MMWS\Controller\FoodController;

class Module extends View
{
    /**
     * Call the create method to create a new user into
     * the database.
     */
    function create(): array
    {
        $hasErrors = keys_match($this->body, ['weight', 'name', 'carb', 'prot', 'tfat', 'sodium', 'cal', 'fiber']);
        if (!$hasErrors) {
            $controller = new FoodController($this->body);
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
    function get(): array
    {
        $controller = new FoodController($this->params);
        return $controller->get($this->query);
    }

    /**
     * Call the update method to update a single user
     * in the database
     */
    function update()
    {
        if (array_key_exists('id', $this->params)) {
            $controller = new FoodController($this->body);
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
            $controller = new FoodController($this->params);
            return $controller->delete();
        } else {
            throw RequestExceptionFactory::field(['id']);
        }
    }
}

/**
 * @var MMWS\Handler\Request contains the request data
 */
global $request;
return new Module($request);
