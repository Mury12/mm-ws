<?php

namespace MMWS\Interfaces;

class AbstractEntity 
{
    protected $model;

    public function __get(string $name) 
    {
        return $this->{$name} ?? null;
    }
}