<?php

namespace Usf\Base\Interfaces;

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
    public function getConfig();

    /**
     * Get configuration value
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * Set section
     * @param string $section
     */
    public function setSection($section);

    /**
     * Set configuration
     * @param array $config
     * @param string $section
     */
    public function setConfig(array $config, $section = null);

    /**
     * Set configuration value
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * Save configuration to file
     * @return bool
     */
    public function save();
}