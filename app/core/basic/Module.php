<?php

abstract class Module
{

    const VERSION_PATTERN = "~^(([0-9]).([0-9]{1,2})(.([0-9]{1,4}))?)$~";

    protected $dir;
    protected $controller;
    protected $action;
    protected $params;

    protected $access;

    protected $ready = false;

    public function __construct($controller, $action, $params = [])
    {
        $this->ready = $this->init($controller, $action);
        $this->params = $params;
    }

    protected function init($controller, $action) {
        $controllerName = ucfirst($controller) . 'Controller';
        $result = false;
        if(is_dir($this->dir)) {
            $controllerFile = $this->dir . '/controllers/' . $controllerName . '.php';
            if(is_file($controllerFile)) {
                include_once $controllerFile;
                $this->controller = new $controllerName($action, $this);
                spl_autoload_register([$this, "autoload"]);
                $result = true;
            }
        }
        return $result;
    }

    public function autoload($className) {
        if($this->ready) {
            $directories = scandir($this->dir);
            $skip = array('.', '..');
            foreach ($directories as $dir) {
                if(!in_array($dir, $skip)) {
                    if(is_dir($this->dir . '/' . $dir)) {
                        $filename = $this->dir . '/' . $dir . '/' . $className . '.php';
                        if(file_exists($filename)) {
                            require_once $filename;
                        }
                    }
                }
            }
        }
    }

    public function run() {
        $result = false;
        if($this->ready) {
            $result =  $this->controller->run($this->params);
        }
        return $result;
    }

    public function authenticate() {
        if(method_exists($this->controller, "authenticate")) {
            return $this->controller->authenticate();
        } else {
            return $this->_authenticate();
        }
    }

    public function logout() {
        if(method_exists($this->controller, "logout")) {
            return $this->controller->logout();
        } else {
            return $this->_logout();
        }
    }

    abstract public function _authenticate();

    abstract public function _logout();

}