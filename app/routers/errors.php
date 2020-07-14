<?php
use MMWS\Model\Layout;

return [
    404 => [
        'body' =>
        $l = new Layout,
        $l->page('error/404')
    ]
    ];