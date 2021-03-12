<?php

/**
 * Esta aplicação controla a autenticação do usuário. Ela servirá para limitar o acesso de páginas 
 * tanto a usuários não autenticados quanto ao contrário, isto é, se no momento da definição de 
 * uma rota, for definido Endpoint::permission('not'), o usuário autenticado não terá acesso à página
 * (como uma página de cadastro ou login, por exemplo) e, caso seja como Endpoint::permission('auth'),
 * o usuário não autenticado não poderá acessar a página (como um painel de usuário).
 */

namespace MMWS\Middleware;

use Exception;
use MMWS\Interfaces\IMiddleware;
use MMWS\Handler\JWTHandler;

// require_once('app/util/ploader.php');

class Authentication implements IMiddleware
{
    private $access;
    const TOKEN = USER_AUTHORIZATION_TOKEN;

    function __construct()
    {
        global $endpoint;
        if (is_array($endpoint)) {
            $this->access = $endpoint[0]->getAccessLevel();
        } else {
            $this->access = $endpoint->getAccessLevel();
        }
        return $this->init();
    }

    /**
     * Acts as a verifier. Be sure to follow up the Interfaces\Middleware abstract class
     */
    function action()
    {
        try {
            return $this->access === 'auth'
                ? JWTHandler::verify(self::TOKEN)
                : ($this->access  === 'not'
                    ? !JWTHandler::verify(self::TOKEN)
                    : true);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Initiates the middleware
     */
    function init()
    {
        if ($this->access) {
            return $this->action();
        }
        return true;
    }
}
