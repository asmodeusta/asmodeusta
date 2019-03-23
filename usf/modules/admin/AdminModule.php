<?php

namespace Usf\Admin;

use Usf\Core\Base\Module;

class AdminModule extends Module
{

    protected $autoloader;

    public function __construct()
    {
        parent::__construct();
        $this->autoloader = new AutoloaderNamespaces( dirname( __FILE__ ), __NAMESPACE__ );
    }

    public function checkAccess()
    {
        return true;
    }

}