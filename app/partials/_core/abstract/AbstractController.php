<?php

namespace MMWS\Interfaces;

class AbstractController 
{
    private $model;

    /**
     * Saves this instance to the database
     */
    public function save()
    {
        return $this->model->save();
    }

    /**
     * Updates this instance to the database
     */
    public function update()
    {
        return $this->model->update();
    }

    /**
     * Get one instance from the database
     */
    public function get(array $filters = [], bool $asobj = false)
    {
        return $this->model->get($filters, $asobj);
    }

    /**
     * Get all the instances from the database and returns 
     * as an SELF::CLASS array
     */
    public function getAll(array $filters = [], bool $asobj = false)
    {
        return $this->model->getAll($filters, $asobj);
    }

    /**
     * Removes this instance from the database
     */
    public function delete()
    {
        return $this->model->delete();
    }
}