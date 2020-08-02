<?php

namespace MMWS\Model;

use MMWS\Model\Request;

class Endpoint
{

    private $_env = array();
    private $api = true;
    private $access = 'any';
    private $route;
    private $request;
    public $procedure;
    public $body = array();

    function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Este método retorna os arquivos parciais que você inseriu em @method appendPartials()
     * @return file de arquivos
     */
    public function getPartials()
    {
        return require_once $this->partials;
    }

    public function error($page)
    {
        $this->request->add("ERROR", $page, '');
        return $this;
    }
    public function post($page, $procedure, Int $v = 2)
    {
        $this->request->add("POST", $page, $procedure);
        return $this;
    }

    public function patch($page, $procedure, Int $v = 2)
    {
        $this->request->add("PATCH", $page, $procedure);
        return $this;
    }

    public function put($page, $procedure, Int $v = 2)
    {
        $this->request->add("PUT", $page, $procedure);
        return $this;
    }

    public function get($page, $procedure, Int $v = 2)
    {
        $this->request->add("GET", $page, $procedure);
        return $this;
    }

    public function delete($page, $procedure, Int $v = 2)
    {
        $this->request->add("DELETE", $page, $procedure);
        return $this;
    }

    private function getRequestParams($prepare = false)
    {
        global $body;
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        if ($prepare && str_in($method, ['POST', 'PUT', 'PATCH'])) {
            $fn = strtolower($method) . '_params';
            $body = $fn();
        }
        return $method;
    }


    /**
     * Este método renderiza a página principal de seu site, configurado em @method page()
     * @return file
     */
    public function render()
    {
        global $params;
        global $procedure;
        $method = $this->getRequestParams(true);

        if ($req = $this->request->get($method)) {
            $params = $this->getEnv();
            $procedure = $req['procedure'];
            extract($params);
            return file_exists($req['page']) ? require_once $req['page'] : die(send(error_message(500)));
        } else {
            die(send(error_message(405)));
        }
    }

    /**
     * Este é o método responsável por armazenar as variáveis de ambiente utilizadas dinamicamente nas
     * páginas as quais você as definiu. O vetor é extraído e cada uma de suas chaves é transformada em uma
     * variável diferente para você usar como desejar.
     * @param Array env vetor de variáveis, devem ter um rótulo.
     * @example $l->setEnv(['motivo' => 'Esta é uma realização!', 'amor' => 'Desenvolver páginas lindas!']);
     * --. Em sua página, basta inserir <?= $motivo ?> e <?= $amor ?> onde quiser e seus valores serão impressos :)
     */
    public function setEnv(array $env)
    {
        foreach ($env as $k => $v) {
            $this->_env[$k] = $v;
        }
        return $this;
    }

    /**
     * Este método é responsável por retornar as variáveis criadas dinamicamente.
     * @return array com as variáveis a serem extraidas.
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * Configura a página como uma página de requisições.
     */
    public function isApi($bool = false)
    {
        if ($bool) {
            $this->api = true;
        }
        return $this->api;
    }

    /**
     * Configura o tipo de usuário que pode acessar a página, sendo 'auth' para somente autenticado,
     * 'any' para todos os usuários (padrão) e 'not' para somente usuários não autenticados.
     */
    public function permission($level)
    {
        $this->access = $level;
        return $this;
    }

    /**
     * Retorna o estado da necessidade de autenticação de uma página.
     */
    public function getAccessLevel()
    {
        return $this->access;
    }

    public function setRouteName($route)
    {
        $this->route = $route;
        return $this;
    }

    public function getRouteName()
    {
        return $this->route;
    }

    /**
     * Retorna o conteúdo de um arquivo localizado em 'app/partials/pieces' podendo ser integrado em outra
     * página.
     * @var file nome do arquivo sem extensão.
     * @return string com o conteúdo do arquivo.
     */
    public function getFilePartial($file)
    {
        return \file_get_contents('app/partials/pieces/' . $file . '.php');
    }
}
