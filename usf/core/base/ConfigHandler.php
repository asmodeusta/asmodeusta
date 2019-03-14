<?php

namespace Usf\Core\Base;

use Usf\Core\Base\Interfaces\ConfigHandlerInterface;

/**
 * Class ConfigHandler
 * @package Usf\Core\Base
 */
abstract class ConfigHandler extends Component implements ConfigHandlerInterface
{

    /**
     * Write file when class destructs
     */
    protected const WRITE_ON_DESTRUCT = false;

    /**
     * Config file
     * @var string
     */
    protected $file;

    /**
     * Config file - uses if file does not exists
     * @var string
     */
    protected $filePath;

    /**
     * File match
     * @var string
     */
    protected $fileMatch = '~\.([\w]+)$~';

    /**
     * Section of config file
     * @var string
     */
    protected $section = 'main';

    /**
     * Configuration
     * @var array
     */
    protected $configuration = [];

    /**
     * Modified flag
     * @var bool
     */
    protected $modified = false;

    /**
     * ConfigHandler constructor.
     * @param string $file
     * @param string|null $section
     */
    public function __construct( $file, $section = null )
    {
        if ( $this->validateFile( $file ) ) {
            $this->file = $file;

            if ( ! is_null( $section ) ) {
                $this->section = $section;
            }

            $this->read();
        }
        $this->filePath = $file;
    }

    /**
     * ConfigHandler destructor.
     */
    public function __destruct()
    {
        if ( static::WRITE_ON_DESTRUCT && $this->modified ) {
            $this->write();
        }
    }

    /**
     * Validate file
     * @param $file
     * @return bool
     */
    protected function validateFile( $file )
    {
        $result = false;
        if ( is_file( $file ) ) {
            $result = (bool) preg_match( $this->fileMatch, $file );
        }
        return $result;
    }

    /**
     * Check if section exists
     * @param string $section
     * @return bool
     */
    protected function checkSectionExists( $section )
    {
        return array_key_exists( $section, $this->configuration )
            && is_array( $this->configuration[ $section ] );
    }

    /**
     * Check if THIS section exists
     * @return bool
     */
    protected function checkThisSectionExists()
    {
        return $this->checkSectionExists( $this->section );
    }

    /**
     * Read configuration from file
     * @return boolean
     */
    abstract protected function read();

    /**
     * Write configuration to file
     * @return boolean
     */
    abstract protected function write();

    /**
     * Get configuration
     * @return array
     */
    public function getConfig()
    {
        $result = [];
        if ( $this->checkThisSectionExists() ) {
            $result = $this->configuration[ $this->section ];
        }
        return $result;
    }

    /**
     * Get configuration value
     * @param string $name
     * @return mixed
     */
    public function get( $name ) {
        $result = null;
        $configuration = $this->getConfig();
        if ( array_key_exists( $name, $configuration ) ) {
            $result = $configuration[ $name ];
        }
        return $result;
    }

    /**
     * Get configuration value
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        return $this->get( $name );
    }

    /**
     * Set section
     * @param string $section
     * @return ConfigHandler
     */
    public function setSection( $section )
    {
        if ( is_string( $section ) ) {
            $this->section = $section;
        }
        return $this;
    }

    /**
     * Set configuration
     * @param array $config
     * @param string $section
     */
    public function setConfig( array $config, $section = null )
    {
        $this->setSection( $section );
        if ( $this->configuration[ $this->section ] !== $config ) {
            $this->configuration[ $this->section ] = $config;
            $this->modified = true;
        }
    }

    /**
     * Set configuration value
     * @param string $name
     * @param mixed $value
     * @return ConfigHandler
     */
    public function set( $name, $value )
    {
        if ( $this->checkThisSectionExists() ) {
            if ( ! ( array_key_exists( $name, $this->configuration[ $this->section ] )
                && $this->configuration[ $this->section ][ $name ] === $value ) ) {
                $this->configuration[ $this->section ][ $name ] = $value;
                $this->modified = true;
            }
        }
        return $this;
    }

    /**
     * Set configuration value
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name, $value )
    {
        $this->set( $name, $value );
    }

}