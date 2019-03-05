<?php


class Core
{

    private static $instance;

    private $Db;
    private $Renderer;

    final public static function go() {
        if(!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __construct() {

    }

    private function init() {

    }

    public function __get($name)
    {
        $result = null;
        switch ($name) {
            case 'Db':
                $result = $this->Db;
                break;
            case 'Renderer':
                $result = $this->Renderer;
                break;
            default:
                break;
        }
        return $result;
    }

}