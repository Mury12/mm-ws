<?php

namespace MMWS\Handler;

use Dotenv\Exception\InvalidPathException;

/**
 * MMWS Application class
 * 
 * This is responsible for the server initiation.
 * Be sure to know what you are doing before modifying this.
 * 
 * @author Andre Mury
 * @version 1.0.1-beta
 */
class MMWS
{

    private $env = "";
    private $index = "";

    /**
     * @param string $indexFile the index file inside /initiators
     */
    function __construct(string $indexFile = "index")
    {
        $this->env = $_ENV['APP_ENV'] ?? 'development';
        $this->index = _DEFAULT_STARTER_PATH_ . "/" . implode('.', [$indexFile, $this->env, 'php']);
        if (!file_exists($this->index))
            throw new InvalidPathException("Initiator file not found at " . $this->index . ".", 500);
    }


    /**
     * Threat errors and sends to the client
     * 
     * @param Exception|Error $e the cought error
     */
    private function catchError($e)
    {
        require_once _DEFAULT_APPLICATION_PATH_ . '/functions.php';
        // Logs errros
        report(['error' => json_decode($e, true)]);
        // Sends the error to the client
        set_http_code($e->getCode());
        die(send(http_message($e->getCode(), json_decode($e->getMessage(), true))));
    }

    /**
     * Require files and starts the server with loaded configurations
     */
    function start()
    {
        try {
            return require_once $this->index;
        } catch (RequestException $e) {
            $this->catchError($e);
        } catch (\Error $e) {
            $this->catchError($e);
        }
    }

    /**
     * Alias for MMWS::start().
     */
    function amaze()
    {
        return $this->start();
    }
}
