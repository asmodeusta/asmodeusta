<?php

namespace Usf\Core\Base;

use Usf\Core\Base\Interfaces\ModuleInterface;

abstract class Module implements ModuleInterface
{
    protected $reflector;

    protected $dir;

    public function __construct()
    {
        $this->reflector = new \ReflectionClass( get_class( $this ) );
        $this->dir = dirname( $this->reflector->getFileName() );
    }

    public function getControllerFile( $controllerClassName )
    {
        $controllerFilename = $this->dir . '/controllers/' . $controllerClassName . '.php';
        if ( ! is_file( $controllerFilename ) ) {
            $controllerFilename = null;
        }
        return $controllerFilename;
    }

    public function getController( $controllerName )
    {
        $controller = null;
        $controllerClassName = ucfirst( $controller ) . 'Controller';
        if ( $controllerFilename = $this->getControllerFilename( $controllerClassName ) ) {
            require_once $controllerFilename;
            $controller = new $controllerClassName;
        }
        return $controller;
    }


}