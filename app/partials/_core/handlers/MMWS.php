<?php

namespace MMWS\Handler;

use Dotenv\Exception\InvalidPathException;

class MMWS
{

    private $env = "production";
    private $index = "";

    function __construct(string $env = "development", string $indexFile = "index")
    {
        $this->env = $env;
        $this->index = _DEFAULT_STARTER_PATH_ . "/" . implode('.', [$indexFile, $this->env, 'php']);
        if (!file_exists($this->index))
            throw new InvalidPathException("Initiator file not found at " . $this->index . ".", 500);
    }


    function start()
    {
        try {
            return require_once $this->index;
        } catch (RequestException $e) {
            require_once './app/functions.php';

            report(['error' => $e->getRequest()]);

            throw $e;
        } catch (\Error $te) {
            require_once _DEFAULT_APPLICATION_PATH_ . '/functions.php';

            report(['error' => json_decode($te, true)]);

            throw $te;
        }
    }

    function amaze()
    {
        return $this->start();
    }
}
