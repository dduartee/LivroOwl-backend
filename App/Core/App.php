<?php

namespace App\Core;

class App
{
    
    private $controller = 'Home';
    private $method = 'index';
    private $params = [];
    private $controllerFolder = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR;

    /**
     * chama o controller com o metodo e os parametros de acordo com a uri
     */
    public function __construct()
    {
        $URI = $this->parseURI(filter_input(INPUT_SERVER, 'REQUEST_URI') ? $_SERVER['REQUEST_URI'] : "");
        $this->getController($URI);
        $this->getMethod($URI);
        $this->getParams($URI);

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * transforma a uri em array separando entre as /
     *
     * @param string $URI
     * @return array
     */
    private function parseURI(string $URI)
    {
        return explode('/', substr($URI, 1));
    }

    /**
     * Verifica a existencia do controller e chama ele
     *
     * @param array $URI
     * @return bool
     */
    private function getController(array $uri) 
    {
        if(!empty($uri[0]) && isset($uri[0]))
        {
            $controller = ucfirst($uri[0]); // deixa a primeira letra maiuscula

            $controllerFile = $this->controllerFolder.$controller.'.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controller;
            } else {
                $this->controller = 'Error';
            }
        }
        $controllerClass = "App\\Controllers\\".$this->controller;
        $this->controller = new $controllerClass;
        return 1;
    }

    /**
     * Verifica a existencia do metodo dentro do controller
     *
     * @param array $uri
     * @return bool
     */
    private function getMethod(array $uri) 
    {
        if(!empty($uri[1]) && isset($uri[1]))
        {
            $method = lcfirst($uri[1]);
            if(method_exists($this->controller, $method) && !$this->controller == 'Error') {
                $this->method = $method;
                return 1;
            } else {
                http_response_code(404);
                $this->method = 'index';
            }
        }
        return 0;
    }

    /**
     * Separa o controller, method e os parametros da url
     *
     * @param array $uri
     * @return bool
     */
    private function getParams(array $uri)
    {
        if(count($uri) > 2) {
            $this->params = array_slice($uri, 2);
            return 1;
        }
        return 0;
    }
}