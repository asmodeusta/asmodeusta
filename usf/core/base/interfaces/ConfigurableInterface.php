<?php

namespace Usf\Core\Base\Interfaces;

/**
 * Interface ConfigurableInterface
 * @package Usf\Core\Base\Interfaces
 */
interface ConfigurableInterface
{
    /**
     * @param array $config
     */
    function setupConfig( array $config );

    /**
     * @param string $file
     */
    function setupConfigFromFile( $file );
}