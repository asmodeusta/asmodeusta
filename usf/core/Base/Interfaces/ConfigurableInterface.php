<?php

namespace Usf\Base\Interfaces;

/**
 * Interface ConfigurableInterface
 * @package Usf\Core\Base\Interfaces
 */
interface ConfigurableInterface
{
    /**
     * @param array $config
     */
    public function setupConfig(array $config);

    /**
     * @param string $file
     */
    public function setupConfigFromFile($file);
}