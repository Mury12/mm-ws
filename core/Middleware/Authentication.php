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
use MMWS\Factory\RequestExceptionFactory;
use MMWS\Interfaces\Middleware;
use MMWS\Handler\JWTHandler;
use MMWS\Handler\Request;

// require_once('src/util/ploader.php');

class Authentication implements Middleware
{
    private $access;
    const TOKEN = USER_AUTHORIZATION_TOKEN;

    const NOT_AUTH = 0;
    const AUTH = 1;
    const ANY_ACCESS = 2;

    function __construct(int $permission = self::AUTH)
    {
        $this->access = $permission;
    }

    /**
     * Acts as a verifier. Be sure to follow up the Interfaces\Middleware abstract class
     */
    function action()
    {
        try {
            $verified = JWTHandler::verify(self::TOKEN);
            if (
                $this->access === self::AUTH && !$verified
                || $this->access ===  self::NOT_AUTH && $verified
            ) {
                throw RequestExceptionFactory::create("User must be logged in.", 401);
            }
        } catch (Exception $e) {
            throw RequestExceptionFactory::create($e->getMessage(), 401);
        }
    }

    /**
     * Initiates the middleware
     */
    function init(?Request $request)
    {
        if ($this->access) {
            return $this->action();
        }
        return true;
    }
}
