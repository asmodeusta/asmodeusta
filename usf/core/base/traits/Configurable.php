<?php

namespace Usf\Core\Base\Traits;

use Usf\Core\Base\ConfigHandler;
use Usf\Core\Base\Factories\ConfigHandlerFactory;

/**
 * Trait Configurable
 * @package Usf\Core\Base\Traits
 */
trait Configurable
{

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var string
     */
    protected $configFile;

    /**
     * @var ConfigHandler
     */
    protected $configHandler;

    /**
     * Saving configuration
     */
    protected function saveConfiguration()
    {
        $this->configHandler->setFullConfig( $this->configuration )->save();
    }

    /**
     * Configure $this object
     * @return $this
     */
    public function configure()
    {
        $this->configHandler = ConfigHandlerFactory::create( $this->configFile );
        $this->configuration = $this->configHandler->getFullConfig();
        $this->setup();
        return $this;
    }

    /**
     * Setter for $this->>configFile
     * @param string $file
     * @return $this
     */
    public function setConfigFile( $file )
    {
        $this->configFile = $file;
        return $this;
    }

    /**
     * Setup $this object, based on configuration
     * @return mixed
     */
    abstract protected function setup();

}