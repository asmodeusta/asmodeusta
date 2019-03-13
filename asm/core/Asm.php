<?php

namespace Core;

use Core\Components\AsmClassAutoloader;
use Core\Components\Router;

class Asm
{

    private static $instance = null;

    private $autoloader;
    private $router;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        require_once ROOT . '/asm/core/components/AsmClassAutoloader.php';
        $this->autoloader = new AsmClassAutoloader( dirname(__FILE__), __NAMESPACE__ );
        $this->router = new Router();

        $routesJson = file_get_contents( ROOT . '/asm/core/config/routes.json' );
        $routes = json_decode( $routesJson, true, 512, JSON_BIGINT_AS_STRING );
        $defaults = [
            'module' => 'site',
            'controller' => 'main',
            'action' => 'index'
        ];
        $this->router->addRoutes( $routes )->setDefaults( $defaults )->parseRequest();
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    private function __sleep()
    {
        // TODO: Implement __sleep() method.
    }

    private function __wakeup()
    {
        // TODO: Implement __wakeup() method.
    }


}

function Asm() {
    return Asm::instance();
}