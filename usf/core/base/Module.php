<?php

namespace Usf\Core\Base;

use Usf\Core\Base\Exceptions\ModuleException;
use Usf\Core\Base\Interfaces\ModuleInterface;

abstract class Module extends Component implements ModuleInterface
{

    /**
     * @param string $controllerClassName
     * @return string
     * @throws ModuleException
     * @throws \ReflectionException
     */
    public function getControllerFile( $controllerClassName )
    {
        $controllerFilename = $this->getDirectory() . '/controllers/' . $controllerClassName . '.php';
        if ( ! is_file( $controllerFilename ) ) {
            throw new ModuleException( 'Controller class "' . $controllerClassName . '" not found!' );
        }
        return $controllerFilename;
    }

    /**
     * @param string $controllerName
     * @return Controller|null
     * @throws ModuleException
     * @throws \ReflectionException
     */
    public function getController( $controllerName )
    {
        $controller = null;
        $controllerClassName = ucfirst( $controllerName ) . 'Controller';
        if ( $controllerFile = $this->getControllerFile( $controllerClassName ) ) {
            require_once $controllerFile;
            $controller = new $controllerClassName;
        }
        return $controller;
    }


}