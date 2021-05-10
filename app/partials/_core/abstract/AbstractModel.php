<?php

namespace MMWS\Interfaces;

use MMWS\Handler\CaseHandler;

class AbstractModel
{
    public $entity;

    /**
     * @var String $table the table name for this model;
     */
    public $table = 'avaliacao';

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

    /**
     * Returns an instance of the object as an array format,
     * the NULL values will not be contained in this array.
     *
     * This is an useful method when desired to obtain the
     * database column names in the right format and its values,
     * when using the built-in PDOQueryBuilder.
     *
     * ```php
     *  $fields = $model->toArray();
     *  $stmt = new PDOQueryBuilder($model->table);
     *  $stmt->insert($fields);
     *  $stmt->run();
     * ```
     *
     * @param string[] $skip props to skip when converting
     * @param bool $snake if should convert to snake_case.
     * @return string[]
     */
    public function toArray(array $skip = [], $snake = true): array
    {
        $arr = [];
        foreach ((array) $this as $key => $prop) {
            if (!(preg_match('/entity/im', $key) || $key === 'table' || array_search($key, $skip) !== false) && $prop) {
                $k = $snake
                    ? CaseHandler::convert($key, 1)
                    : $key;
                $arr[$k] = $prop;
            }
        }
        return $arr;
    }
}
