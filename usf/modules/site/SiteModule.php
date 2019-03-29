<?php

namespace Usf\Site;

use Usf\Core\Base\Module;
use Usf\Core\Src\AutoloaderNamespaces;

class SiteModule extends Module
{

    protected $autoloader;

    public function __construct( $controller, $action )
    {
        parent::__construct( $controller, $action );
        $this->autoloader = new AutoloaderNamespaces( dirname( __FILE__ ), __NAMESPACE__ );
    }

}