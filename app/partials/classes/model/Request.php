<?php

namespace MMWS\Model;

class Request
{
    private $request = [];

    function __construct()
    {
    }

    public function add(String $method, String $page, String $procedure)
    {
        $this->request[strtoupper($method)] = [
            'page' => $this->page($page),
            'procedure' => $procedure
        ];
        return $this;
    }

    public function get(String $method)
    {
        if (array_key_exists(strtoupper($method), $this->request)) {
            return $this->request[strtoupper($method)];
        }
        return false;
    }

    /**
     * Esta função é responsável por dizer qual é a página a ser carregada no corpo de seu site.
     * @param $page é o diretório da página, já considerando estar na pasta correta app/pages.
     * @param Int $v é a versão do webservice. Default 2
     */
    private function page($page, Int $v = 2)
    {
        return 'app/_ws/v' . $v . '/' . $page . '.php';
    }
}
