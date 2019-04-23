<?php

namespace Usf\Base;

use Usf\Base\Interfaces\ExtensionInstallationInterface;
use Usf\Base\Traits\Configurable;

abstract class Extension extends Component implements ExtensionInstallationInterface
{

    use Configurable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $slag;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var array|string
     */
    protected $author;

    /**
     * @var bool
     */
    protected $active;

    public function __construct()
    {
        $this->initialize();
    }

    public function save()
    {
        $this->saveInformation()->saveConfiguration();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlag()
    {
        return $this->slag;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return array|string
     */
    public function getAuthor()
    {
        return $this->author;
    }


    /**
     * Initialize
     * @return $this
     */
    protected function initialize()
    {
        $configDir = $this->getDirectory() . DS . 'config';
        $configFile = $configDir . DS . 'info.config.json';
        if (is_file($configFile)) {
            $this->setConfigFile($configFile);
        }
        if ( empty($this->configuration) ) {
            $this->configure();
        } else {
            $this->setup();
        }
        return $this;
    }

    /**
     * Setup
     */
    protected function setup()
    {
        $this->setupInformation();
    }

    /**
     * Setup information
     * @return $this
     */
    protected function setupInformation()
    {
        $this->slag = $this->configuration['slag'] ?? null;
        $this->name = $this->configuration['name'] ?? null;
        $this->description = $this->configuration['description'] ?? null;
        $this->version = $this->configuration['version'] ?? null;
        $this->author = $this->configuration['author'] ?? null;
        $this->active = $this->configuration['author'] ?? false;
        return $this;
    }

    /**
     * Save information
     * @return $this
     */
    protected function saveInformation()
    {
        $this->configuration['slag'] = $this->slag;
        $this->configuration['name'] = $this->name;
        $this->configuration['description'] = $this->description;
        $this->configuration['version'] = $this->version;
        $this->configuration['author'] = $this->author;
        $this->configuration['active'] = $this->active;
        return $this;
    }

}