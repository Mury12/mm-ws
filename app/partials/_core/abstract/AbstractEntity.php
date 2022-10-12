<?php

namespace MMWS\Interfaces;

class AbstractEntity
{
    protected $model;
    protected $table;

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }
}
