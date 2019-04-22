<?php

namespace Usf\Base;

use Usf\Base\Interfaces\ExtensionInstallationInterface;
use Usf\Base\Traits\Configurable;

abstract class Extension extends Component implements ExtensionInstallationInterface
{

    use Configurable;

    protected $id;

    protected $slag;

    protected $name;

    protected $description;

    protected $version;

    protected $author;

    protected $active;

    public function __construct()
    {
        $this->initialize();
    }

    public function initialize()
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
    }

    protected function setup()
    {
        $this->slag = $this->configuration['slag'] ?? null;
        $this->name = $this->configuration['name'] ?? null;
        $this->description = $this->configuration['description'] ?? null;
        $this->version = $this->configuration['version'] ?? null;
        $this->author = $this->configuration['author'] ?? null;
        $this->active = $this->configuration['author'] ?? false;
    }

}