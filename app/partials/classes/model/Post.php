<?php

/**
 * It's a default model class
 */

namespace MMWS\Model;

use MMWS\Entity\PostEntity;

class Post
{
    /**
     * @var PostEntity $entity is the database entity
     */
    private $entity;

    /**
     * @var String $name the post name
     */
    public $name;
    
    /**
     * @var String $content the post content
     */
    public $content;

    function __construct(array $request)
    {
        foreach ($request as $key => $value)
            $this->{$key} = $value;

        $this->entity = new PostEntity($this);
    }

    /**
     * Gets the Post model props
     * @return Post
     */
    function get()
    {
        return $this;
    }

    /**
     * Saves a post into the database
     * @return Bool
     */
    function save()
    {
        return $this->entity->save();
    }
}
