<?php

namespace Usf\Core\Base;

use Usf\Core\Base\Exceptions\ModuleException;
use Usf\Core\Base\Interfaces\ModuleInterface;

/**
 * Class Module
 * @package Usf\Core\Base
 */
abstract class Module extends Component implements ModuleInterface
{

    /**
     * @var string
     */
    protected $basename;

    /**
     * Controller
     * @var Controller|null
     */
    protected $controller = null;

    /**
     * Views
     * @var array
     */
    protected $views = [];

    /**
     * Module constructor.
     */
    public function __construct()
    {
        $this->basename = basename( dirname( $this->getReflector()->getFileName() ) );
    }

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
     * @param $viewClassName
     * @return string
     * @throws ModuleException
     * @throws \ReflectionException
     */
    public function getViewFile( $viewClassName )
    {
        $viewFileName = $this->getDirectory() . '/views/' .  $viewClassName . '.php';
        if ( ! is_file( $viewFileName ) ) {
            throw new ModuleException( 'View class "' . $viewFileName . '" not found!' );
        }
        return $viewFileName;
    }

    /**
     * @param string $name
     * @return Controller|null
     */
    public function getController( $name = '' )
    {
        if ( $name === '' ) {
            return $this->controller;
        } else {
            return $this->generateController( $name );
        }
    }

    /**
     * @param $name
     * @return Controller|null
     */
    protected function generateController( $name )
    {
        $controllerClassName = ucfirst( $name ) . 'Controller';
        try {
            $controllerFile = $this->getControllerFile( $controllerClassName );
            require_once $controllerFile;
            $controllerClass = lastDeclaredClass();
            $this->controller = new $controllerClass( $this );
        } catch ( ModuleException $exception ) {
            $this->addErrorMessage( $exception->getMessage() );
        } catch ( \ReflectionException $exception ) {
            $this->addErrorMessage( $exception->getMessage() );
        }
        return $this->controller;
    }

    /**
     * @param string $name
     * @param array $data
     * @return bool|View
     */
    public function getView( $name, $data = [] )
    {
        try {
            if ( ! array_key_exists( $name, $this->views ) ) {
                $viewClassName = ucfirst( $name ) . 'View';
                $viewFile = $this->getViewFile( $viewClassName );
                require_once $viewFile;
                $this->views[ $name ] = lastDeclaredClass();
            }
            $viewClass = $this->views[ $name ];
            return new $viewClass( $this, $data );
        } catch ( ModuleException $exception ) {
            $this->addErrorMessage( $exception->getMessage() );
        } catch ( \ReflectionException $exception ) {
            $this->addErrorMessage( $exception->getMessage() );
        } catch ( \Exception $exception ) {
            $this->addErrorMessage( $exception->getMessage() );
        }
        return false;
    }

    /**
     * Searching for template filename
     *
     * @param string $name
     * @return bool|string
     */
    public function getTemplateFile( $name )
    {
        // Make filename with nodule name and extension
        $filename = $this->basename . DS . $name . '.php';

        // Check if file exists in selected theme
        $themeFile = DIR_USF . DS . 'themes' . DS . usf()->settings->getTheme() . DS . $filename;
        if ( is_file( $themeFile ) ) {
            return $themeFile;
        }

        // Check if file exists in module directory
        $moduleFile = DIR_MODULES . DS . $filename;
        if ( is_file( $moduleFile ) ) {
            return $moduleFile;
        }

        // If not found - return false
        return false;
    }

}