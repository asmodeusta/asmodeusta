<?php

namespace Usf\Admin;

use Usf\Base\Module;
use Usf\Src\AutoloaderNamespaces;

class AdminModule extends Module
{

    protected $autoloader;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
        $this->autoloader = new AutoloaderNamespaces(dirname(__FILE__), __NAMESPACE__);
    }

    public function checkAccess()
    {
        return true;
    }

}