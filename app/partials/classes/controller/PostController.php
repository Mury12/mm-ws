<?php

namespace MMWS\Controller;

use MMWS\Model\Post;

/**
 * This is a default controller class
 */
class PostController
{
    /**
     * @var Post $post the model instance for the class
     */
    private $post;

    function __construct(Array $request)
    {
        $this->post = new Post($request);
    }

    /**
     * Gets the Post props
     * @return Post
     */
    function get() 
    {
        return $this->post->get();
    }

    /**
     * Saves a post into the database
     * @return Bool
     */
    function save()
    {
        return $this->post->save();
    }
}