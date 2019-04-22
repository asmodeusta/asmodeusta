<?php

namespace Usf\Base;

use Usf\Base\Exceptions\ControllerException;

/**
 * Class Controller
 * @package Usf\Core\Base
 */
abstract class Controller extends Component
{
    /**
     * Module
     * @var Module
     */
    protected $module;

    /**
     * Base name
     * @var string
     */
    protected $basename;

    /**
     * Controller constructor.
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
        $shortName = $this->getReflector()->getShortName();
        $this->basename = lcfirst(substr($shortName, 0, strpos('Controller', $shortName)));
    }

    /**
     * @param string|null $actionName
     * @return bool
     */
    public function checkAccess($actionName = null)
    {
        if (is_string($actionName) && preg_match('~(.*)~', $actionName)) {
            $access = true;
        } else {
            $access = true;
        }
        return $access && $this->module->checkAccess();
    }

    /**
     * Get action
     * @param $actionName
     * @return callable
     * @throws ControllerException
     */
    public function getAction($actionName)
    {
        //$action = [ $this, 'actionDefaultError' ];
        $actionMethodName = 'action' . ucfirst($actionName);
        if ($this->checkAccess($actionName) && method_exists($this, $actionMethodName)) {
            $action = [$this, $actionMethodName];
        } else {
            throw new ControllerException('Action "' . $actionName . '" does not exist!');
        }
        return $action;
    }

    /**
     * Get Base name
     * @return string
     */
    public function getBaseName()
    {
        return $this->basename;
    }

    /**
     * Default error action
     */
    public function actionDefaultError()
    {
        echo 'Page not found(';
    }

}