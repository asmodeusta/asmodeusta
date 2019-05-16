<?php


namespace Usf\Base\Interfaces;


interface ControllerInterface
{

    /**
     * Get action by name
     * @param string $actionName
     * @return callable|false
     */
    public function getAction(string $actionName);

    /**
     * Check access for current action
     * @return bool
     */
    public function checkAccess() : bool ;

}