<?php

namespace MMWS\Interfaces;

use Error;
use MMWS\Handler\CaseHandler;

class AbstractModel
{

    /**
     * @var String $table the table name for this model;
     */
    public $table = '';

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
            if (!($key === 'table' || array_search($key, $skip) !== false) && $prop) {
                $sanitizedKey = preg_replace('/\W+/i', '', $key);
                $k = $snake
                    ? CaseHandler::convert($sanitizedKey, 1)
                    : $sanitizedKey;
                $arr[$k] = $prop;
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
            $this->{$name} = $value;
        } else {
            throw new Error("Property $name does not exists for object of type '" . self::class . "'.");
        }
    }
}
