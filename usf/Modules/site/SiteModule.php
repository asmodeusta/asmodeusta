<?php

namespace Usf\Site;

use Usf\Core\Base\Module;
use Usf\Core\Src\AutoloaderNamespaces;
use Usf\Site\Models\NewModel;

class SiteModule extends Module
{

    protected $autoloader;

    public function __construct( $controller, $action )
    {
        parent::__construct( $controller, $action );
        usf()->autoloader()->addPsr4( __NAMESPACE__ . '\\', dirname( __FILE__ ) );
    }

}