<?php

namespace Usf\Site;

use Usf\Base\Module;

class SiteModule extends Module
{

    protected $autoloader;

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
        usf()->autoloader()->addPsr4(__NAMESPACE__ . '\\', dirname(__FILE__));
    }

}