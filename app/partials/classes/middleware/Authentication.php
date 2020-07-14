<?php

/**
 * Esta aplicação controla a autenticação do usuário. Ela servirá para limitar o acesso de páginas 
 * tanto a usuários não autenticados quanto ao contrário, isto é, se no momento da definição de 
 * uma rota, for definido Layout::permission('not'), o usuário autenticado não terá acesso à página
 * (como uma página de cadastro ou login, por exemplo) e, caso seja como Layout::permission('auth'),
 * o usuário não autenticado não poderá acessar a página (como um painel de usuário).
 */

namespace MMWS\Middleware;
use MMWS\Interfaces\Middleware;
use MMWS\Controller\UserController;

require_once('app/util/ploader.php');

class Authentication implements Middleware
{
    private $access;
    private $user;
    const TOKEN = USER_AUTHORIZATION_TOKEN;

    function __construct()
    {
        global $layout;

        $this->access = $layout->getAccessLevel();
        $this->user = new UserController(['session_token' => self::TOKEN]);
        return $this->init();
    }

    function action()
    {
        return $this->access === 'auth'
            ? $this->user->verify()
            : $this->access  === 'not'
            ? !$this->user->verify()
            : true;
    }
    function init()
    {
        if ($this->access) {
            return $this->action();
        }
    }
}
