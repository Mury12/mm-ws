<?php

namespace MMWS\Output;

class JwtResponse
{
    public string $jwt;

    function __construct(string $jwt)
    {
        $this->jwt = $jwt;
    }
}
