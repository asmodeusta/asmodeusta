<?php

namespace Usf\Core\Base;

use Usf\Core\Base\Interfaces\ConfigurableInterface;
use Usf\Core\Base\Factories\ConfigHandlerFactory;

/**
 * Class Configurable
 * @package Usf\Core\Base
 */
abstract class Configurable implements ConfigurableInterface
{
    /**
     * @param string $file
     */
    public function setupConfigFromFile( $file )
    {
        $handler = ConfigHandlerFactory::create( $file );
        $this->setupConfig( $handler->getFullConfig() );
    }
}