<?php

namespace Usf\Base;

use Usf\Base\Factories\ConfigHandlerStaticFactory;

/**
 * Class Configuration
 * @package Usf\Base
 */
abstract class Configuration extends Component
{

    /**
     * Config handler
     * @var ConfigHandler
     */
    protected $handler;

    /**
     * Shows if configuration was modified
     * @var bool
     */
    protected $modified = false;

    /**
     * Configuration data
     * @var array
     */
    protected $config = [];

    /**
     * Configuration constructor.
     * @param string $file - path of the config file
     */
    public function __construct( $file )
    {
        $this->handler = ConfigHandlerStaticFactory::create( $file );
        $this->read();
    }

    /**
     * Getter for configuration parameter
     * @param string $name - name of configuration value
     * @return mixed - configuration parameter value
     */
    public function __get( $name )
    {
        return $this->get( $name );
    }

    /**
     * Setter for configuration parameter
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name, $value )
    {
        $this->set( $name, $value );
    }

    /**
     * Getter for configuration parameter
     * @param string $name - name of configuration value
     * @return mixed - configuration parameter value
     */
    public function get( $name )
    {
        return $this->config[ $name ] ?? null;
    }

    /**
     * Setter for configuration parameter
     * @param string $name
     * @param mixed $value
     */
    public function set( $name, $value )
    {
        if ( $this->validate( $name, $value ) ) {
            if ( $this->config[ $name ] !== $value ) {
                $this->config[ $name ] = $value;
                $this->modified = true;
            }
        }
    }

    /**
     * Validating configuration item value
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    abstract public function validate( $name, &$value ) : bool;

    /**
     * Refreshing data from config file
     */
    public function refresh()
    {
        $this->read();
    }

    /**
     * Saving configuration
     * @return bool
     */
    public function save()
    {
        if ( $this->modified ) {
            $this->handler->setFullConfig( $this->config );
            if ( $this->handler->save() ) {
                $this->modified = false;
                return true;
            }
        }
        return false;
    }

    /**
     * Reading configuration
     */
    protected function read()
    {
        $this->config = $this->handler->getFullConfig();
    }

}