<?php

namespace Usf\Base;

use Usf\Base\Traits\Cacheable;

/**
 * Class UsfModule
 * @package Usf\Base
 */
class UsfModule extends ModuleExtension
{

    use Cacheable;

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var string
     */
    protected $viewName;

    /**
     * @var UsfController
     */
    protected $controller;

    /**
     * @var callable
     */
    protected $action;

    /**
     * @var UsfView
     */
    protected $view;

    /**
     * @inheritDoc
     */
    public function install() : bool
    {

        return true;
    }

    /**
     * @inheritDoc
     */
    public function activate() : bool
    {

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deactivate() : bool
    {

        return true;
    }

    /**
     * @inheritDoc
     */
    public function uninstall() : bool
    {

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCallback(array $params)
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

    /**
     * @return UsfController|false
     */
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