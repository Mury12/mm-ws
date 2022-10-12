<?php

namespace MMWS\Model;

use Error;
use TypeError;

class DBTableSpec
{
    /**
     * Table name
     */
    protected string $name;
    /**
     * @var DBFieldSpec[] $fields
     */
    protected array $fields;

    /**
     * @param DBFieldSpec[] $fields
     */
    function __construct(string $name, array $fields = [])
    {
        $this->name = $name;
        if ($fields && !$fields[0] instanceof DBFieldSpec) throw new TypeError("Fields must be an array of DBFieldSpec type.", 500);
        $this->fields = $fields;
    }

    /**
     * Return the fields for the table
     * @return DBFieldSpec[]
     */
    function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Return one field of the table
     * @return DBFieldSpec
     */
    function getField($name): DBFieldSpec
    {
        foreach ($this->fields as $field) {
            if ($field->name === $name)
                return $field;
        }
        throw new Error("Field $name not found.");
    }

    /**
     * Add field to the table
     * 
     * @param DBFieldSpec $field
     * @return DBFieldSpec self
     */
    function addField(DBFieldSpec ...$fields): DBTableSpec
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }
}
