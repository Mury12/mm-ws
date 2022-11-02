<?php

namespace MMWS\Abstracts;

class Entity
{
    protected $model;
    protected $table;

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }
}
