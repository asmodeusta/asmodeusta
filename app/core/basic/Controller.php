<?php

class Controller
{
    protected $module;
    protected $action;

    public function __construct($action, $module)
    {
        $this->action = $action;
        $this->module = $module;
    }

    public function run($params = []) {
        $result = false;
        if(isset($this->action)) {
            $methodName = 'action' . ucfirst($this->action);
            if(method_exists($this, $methodName)) {
                try {
                    $result = call_user_func_array(array($this, $methodName), $params) or false;
                } catch (Exception $exception) {
                    $result = false;
                }
            }
        }
        return $result;
    }

}