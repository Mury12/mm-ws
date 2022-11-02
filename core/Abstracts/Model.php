<?php

namespace MMWS\Abstracts;

use Error;
use MMWS\Handler\CaseHandler;

class Model
{

    /**
     * @var String $table the table name for this model;
     */
    protected $hidden = ['hidden'];

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
            $sanitizedKey = preg_replace('/\W+/i', '', $key);
            if (
                array_search($sanitizedKey, array_merge($skip, $this->hidden)) === false
                && $prop !== null
            ) {
                $k = $snake
                    ? CaseHandler::convert($sanitizedKey, 1)
                    : $sanitizedKey;
                $arr[$k] = $prop;
            }
        }
        return $arr;
    }

    /**
     * Returns the table column names for this object even if its value is null.
     *
     * This is an useful method when desired to obtain the
     * database column names in the right format and its values,
     * when using the built-in PDOQueryBuilder.
     *
     * ```php
     *  $fields = $model->getColumnNames();
     *  $stmt = new PDOQueryBuilder($model->table);
     *  $stmt->search($fields, $query);
     * ```
     *
     * @param string[] $skip props to skip when converting
     * @param bool $snake if should convert to snake_case.
     * @return string[]
     */
    public function getColumnNames(array $skip = [], $snake = true): array
    {
        $arr = [];
        foreach ((array) $this as $key => $prop) {
            $sanitizedKey = preg_replace('/\W+/i', '', $key);
            if (array_search($sanitizedKey, array_merge($skip, $this->hidden)) === false) {
                $k = $snake
                    ? CaseHandler::convert($sanitizedKey, 1)
                    : $sanitizedKey;
                $arr[] = $k;
            }
        }
        return $arr;
    }

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }


    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            // $refl = new ReflectionProperty($this, $name);
            // $cast = $refl->getType()->getName();
            // settype($value, $cast);

            $this->{$name} = $value;
        } else {
            throw new Error("Property $name does not exists for object of type '" . self::class . "'.");
        }
    }

    /**
     * Set fields to be hidden when `Model::toArray()` or `Model::getColumnNames()` are called.
     * 
     * It is used to allow relationships in the model without damaging generated queries.
     * 
     * @param string[] $fields fields to hide.
     */
    public function setHiddenFields(array $fields)
    {
        $this->hidden = array_merge($this->hidden, $fields);
    }

    public function resetHiddenFields()
    {
        $this->hidden = ['hidden'];
    }
}
