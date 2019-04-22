<?php

namespace Usf\Base;

use Exception;
use Usf\Base\Exceptions\ModuleException;
use Usf\Base\Interfaces\ModuleInterface;

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
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
    protected $actionName;

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
     * @param $controller
     * @param $action
     * @throws ModuleException
     */
    public function __construct($controller, $action)
    {
        $this->basename = $this->baseName();

        $this->getReflector()->getNamespaceName();

        if ($this->checkAccess()) {
            if ($this->generateController($controller)) {
                if ($this->controller->checkAccess()) {
                    $this->actionName = $action;
                } else {
                    throw new ModuleException('Access denied');
                }
            } else {
                throw new ModuleException('Controller "' . $controller . '" not found!');
            }
        } else {
            throw new ModuleException('Access denied');
        }
    }

    /**
     * Returns Base name of module file
     * Can be overwritten to increase executing speed
     * @return string
     */
    protected function baseName()
    {
        return basename(dirname($this->getReflector()->getFileName()));
    }

    /**
     * Returns action callback
     * @return callable
     * @throws Exceptions\ControllerException
     */
    public function getCallback()
    {
        return $this->controller->getAction($this->actionName);
    }

    /**
     * Checking access to module
     * @return bool
     */
    public function checkAccess()
    {
        return true;
    }

    /**
     * @param string $controllerClassName
     * @return string
     * @throws ModuleException
     */
    public function getControllerFile($controllerClassName)
    {
        $controllerFilename = $this->getDirectory() . '/Controllers/' . $controllerClassName . '.php';
        if (!is_file($controllerFilename)) {
            throw new ModuleException('Controller class "' . $controllerClassName . '" not found!');
        }
        return $controllerFilename;
    }

    /**
     * @param $viewClassName
     * @return string
     * @throws ModuleException
     */
    public function getViewFile($viewClassName)
    {
        $viewFileName = $this->getDirectory() . '/Views/' . $viewClassName . '.php';
        if (!is_file($viewFileName)) {
            throw new ModuleException('View class "' . $viewFileName . '" not found!');
        }
        return $viewFileName;
    }

    /**
     * @param $name
     * @return bool
     */
    protected function generateController($name)
    {
        $result = false;
        $controllerClassName = ucfirst($name) . 'Controller';
        try {
            $controllerFile = $this->getControllerFile($controllerClassName);
            require_once $controllerFile;
            $controllerClass = lastDeclaredClass();
            $this->controller = new $controllerClass($this);
            $this->controllerName = $name;
            $result = true;
        } catch (ModuleException $exception) {
            $this->addErrorMessage($exception->getMessage());
        } catch (Exception $exception) {
            $this->addErrorMessage($exception->getMessage());
        }
        return $result;
    }

    /**
     * @param string $name
     * @param array $data
     * @return bool|View
     */
    public function getView($name, $data = [])
    {
        try {
            if (!array_key_exists($name, $this->views)) {
                $viewClassName = ucfirst($name) . 'View';
                $viewFile = $this->getViewFile($viewClassName);
                require_once $viewFile;
                $this->views[ $name ] = lastDeclaredClass();
            }
            $viewClass = $this->views[ $name ];
            return new $viewClass($this, $data);
        } catch (ModuleException $exception) {
            $this->addErrorMessage($exception->getMessage());
        } catch (Exception $exception) {
            $this->addErrorMessage($exception->getMessage());
        }
        return false;
    }

    /**
     * Searching for template filename
     *
     * @param string $name
     * @return bool|string
     */
    public function getTemplateFile($name)
    {
        // Make filename with nodule name and extension
        $filename = $this->basename . DS . $name . '.php';

        // Check if file exists in selected theme
        $themeFile = DIR_THEMES . DS . usf()->settings()->getTheme() . DS . $filename;
        if (is_file($themeFile)) {
            return $themeFile;
        }

        // Check if file exists in module directory
        $moduleFile = DIR_MODULES . DS . $filename;
        if (is_file($moduleFile)) {
            return $moduleFile;
        }

        // If not found - return false
        return false;
    }

}