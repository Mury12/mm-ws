<?php

namespace MMWS\Abstracts;

use TypeError;

class Controller
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
    public function get(array $filters = [], bool $asobj = true)
    {
        return $this->entity->get($filters, $asobj);
    }

    /**
     * Get all the instances from the database and returns 
     * as an SELF::CLASS array
     */
    public function getAll(array $filters = [], bool $asobj = true)
    {
        return $this->entity->getAll($filters, $asobj);
    }

    /**
     * Updates the model for this controller.
     *
     * @param object $object the instance of object that should fit in the current `model`
     * @throws TypeError if $object is not of the correct class.
     */
    public function setModel(object $object)
    {
        if (get_class($object) === get_class($this->model)) {
            $this->model = $object;
        } else {
            throw new TypeError("Expected `object` to be instance of " . get_class($this->model) . ", found " . get_class($object) . " instead.");
        }
    }

    /**
     * Removes this instance from the database
     */
    public function delete()
    {
        return $this->entity->delete();
    }

    /**
     * Searches the database for the given query, using the columns and skipped columns set. 
     * @param string $query the query
     * @param string[] $columns columns to search
     * @param string[] $skipColumns columns to skip
     * @param bool $asobj if shoult resturn an object
     * @param int $page page
     * 
     * @return array[]|Object[]
     */
    public function search(string $query, array $columns, array $skipColumns = null, bool $asobj = true, int $page = 1)
    {
        return $this->entity->search($query, $columns, $skipColumns, $asobj, $page);
    }

    public function __get(string $name)
    {
        if ($this->model) {
            return $this->model->{$name};
        } else return null;
    }
}
