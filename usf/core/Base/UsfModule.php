<?php

namespace Usf\Base;

/**
 * Class UsfModule
 * @package Usf\Base
 */
class UsfModule extends ModuleExtension
{

    protected $controllerName;
    protected $actionName;
    protected $viewName;

    protected $controller;
    protected $action;
    protected $view;

    public function install() : bool
    {

        return false;
    }

    public function activate() : bool
    {

        return false;
    }

    public function deactivate() : bool
    {

        return false;
    }

    public function uninstall() : bool
    {

        return false;
    }

    /**
     * @param array $params
     * @return callable|false Callable on success. False when callback not found.
     */
    public function getCallback(array $params) : callable
    {
        if ( $controller = array_take('controller', $params) && $action = array_take('action', $params) ) {
            $this->controllerName = $controller;
            $this->actionName = $action;
            if ( $controller = $this->searchController() ) {
                if ($controller instanceof UsfController) {
                    return $controller->getAction($this->actionName);
                }
            }
        }
        return false;
    }

    protected function searchController()
    {
        $namespace = $this->getReflector()->getNamespaceName();
        $controllerClassName = $namespace . '\\Controllers\\' . ucfirst($this->controllerName) . 'Controller';
        if (class_exists($controllerClassName)) {
            return new $controllerClassName($this);
        }
        return false;
    }

}