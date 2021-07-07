<?php

namespace MMWS\Interfaces;

class AbstractController
{
    protected $entity;
    protected $model;

    /**
     * Saves this instance to the database
     */
    public function save()
    {
        return $this->entity->save();
    }

    /**
     * Updates this instance to the database
     */
    public function update()
    {
        return $this->entity->update();
    }

    /**
     * Get one instance from the database
     */
    public function get(array $filters = [], bool $asobj = false)
    {
        return $this->entity->get($filters, $asobj);
    }

    /**
     * Get all the instances from the database and returns 
     * as an SELF::CLASS array
     */
    public function getAll(array $filters = [], bool $asobj = false)
    {
        return $this->entity->getAll($filters, $asobj);
    }

    /**
     * Removes this instance from the database
     */
    public function delete()
    {
        return $this->entity->delete();
    }

    public function __get(string $name)
    {
        if ($this->model) {
            return $this->model->{$name};
        } else return null;
    }
}
