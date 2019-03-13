<?php

namespace Usf\Core\Base\Interfaces;

/**
 * Interface ConfigHandlerInterface
 * @package Usf\Core\Base\Interfaces
 */
interface ConfigHandlerInterface
{
    /**
     * Get configuration
     * @return array
     */
    function getConfig();

    /**
     * Get configuration value
     * @param string $name
     * @return mixed
     */
    function get( $name );

    /**
     * Set section
     * @param string $section
     */
    function setSection( $section );

    /**
     * Set configuration
     * @param array $config
     * @param string $section
     */
    function setConfig( array $config, $section = null );

    /**
     * Set configuration value
     * @param string $name
     * @param mixed $value
     */
    function set( $name, $value );
}