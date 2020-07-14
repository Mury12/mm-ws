<?php

namespace MMWS\Model;

class Layout
{

    private $_env = array();
    private $api = false;
    private $access = 'any';
    private $route;
    private $params = Array();


    /**
     * Esta função é responsável por dizer qual é a página a ser carregada no corpo de seu site.
     * @param $page é o diretório da página, já considerando estar na pasta correta app/pages.
     * @param Int $v é a versão do webservice. Default 2
     */
    public function page($page, Int $v = 2)
    {
        $this->page = 'app/_ws/v' . $v . '/' . $page . '.php';
        return $this;
    }



    /**
     * Este método retorna os arquivos parciais que você inseriu em @method appendPartials()
     * @return file de arquivos
     */
    public function getPartials()
    {
        return require_once $this->partials;
    }

    /**
     * Este método renderiza a página principal de seu site, configurado em @method setPage()
     * @return file
     */
    public function render()
    {
        extract($this->getEnv());
        
        return file_exists($this->page) ? require_once $this->page : send(error_message(500));
    }

    /**
     * Este é o método responsável por armazenar as variáveis de ambiente utilizadas dinamicamente nas
     * páginas as quais você as definiu. O vetor é extraído e cada uma de suas chaves é transformada em uma
     * variável diferente para você usar como desejar.
     * @param Array env vetor de variáveis, devem ter um rótulo.
     * @example $l->setEnv(['motivo' => 'Esta é uma realização!', 'amor' => 'Desenvolver páginas lindas!']);
     * --. Em sua página, basta inserir <?= $motivo ?> e <?= $amor ?> onde quiser e seus valores serão impressos :)
     */
    public function setEnv(Array $env)
    {
        foreach($env as $k => $v){
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
