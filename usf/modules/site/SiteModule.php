<?php

use Usf\Core\Base\Module;
use Usf\Core\Src\AutoloaderNamespaces;

class SiteModule extends Module
{

    protected $autoloader;

    public function __construct()
    {
        $this->autoloader = new AutoloaderNamespaces( dirname( __FILE__ ), __NAMESPACE__ );
    }

}