<?php

class App
{

    private static $instance;

    private $router;
    private $session;
    private $module;
    private $user;
    private $data;

    public static function go() {
        if(!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->router = new Router();
        $this->session = Session::start();
    }

    public function __get($name)
    {
        $result = null;
        if(isset($this->$name)) {
            $result = $this->$name;
        } else {
            $result = $this->get($name);
        }
        return $result;
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __unset($name)
    {
        $this->unset($name);
    }

    public function get($name) {
        $result = null;
        if(isset($this->data[$name])) {
            $result = $this->data[$name];
        }
        return $result;
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function unset($name) {
        unset($this->data[$name]);
    }

    public function start() {
        $url = $_SERVER['REQUEST_URI'];
        if($this->router->run($url, $this->module)) {

        } else {
            echo '404!';
        }
    }

    public function logout() {
        $result = true;
        if(isset($this->module)) {
            $result = $this->session->unsetCurrentUser();
        }
        return $result;
    }

}

function App() {
    return App::go();
}