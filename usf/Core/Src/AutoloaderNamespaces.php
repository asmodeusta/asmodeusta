<?php

namespace Usf\Core\Src;

/**
 * Class AutoloaderNamespaces
 * @package Usf\Core\Src
 */
class AutoloaderNamespaces
{
    /**
     * Root path for loading
     *
     * @var string
     */
    protected $directory = DIR_APP;

    /**
     * Namespace of path
     *
     * @var string
     */
    protected $namespace = 'Root';

    /**
     * File extension
     *
     * @var string
     */
    protected $extension = '.php';

    /**
     * Namespace separator
     *
     * @var string
     */
    protected $separator = '\\';

    /**
     * Collection of Autoloader objects
     *
     * @var array
     */
    protected static $loaders = [];

    /**
     * AutoloaderNamespaces constructor.
     * @param string $directory
     * @param string $namespace
     * @param string $separator
     * @param string $extension
     */
    public function __construct( $directory, $namespace, $separator = '\\', $extension = '.php' )
    {
        $this->separator = $separator;
        $this->extension = $extension;
        if ( is_dir( $directory ) ) {
            $this->directory = $directory;
        }
        if ( $this->validateNamespace( $namespace ) ) {
            $this->namespace = $namespace;
        }

        $this->register();

        self::$loaders[static::class] = $this;
    }

    /**
     * Autoloader method
     *
     * @param string $class
     */
    public function load( $class )
    {
        // Checking if class namespace corresponds with autoloader namespace
        $afterNamespacePos = strlen($this->namespace.$this->separator );
        if ( $this->namespace.$this->separator === substr( $class . $this->separator, 0, $afterNamespacePos ) ) {
            $fileName = '';
            // Check if class has sub namespace
            if ( $lastSepPos = ( strripos($class, $this->separator ) + 1 ) ) {
                $namespace = strtolower( substr( $class, $afterNamespacePos, $lastSepPos-$afterNamespacePos ) );
                $class = substr( $class, $lastSepPos );
                $fileName = str_replace( $this->separator, DS, $namespace );
            }
            // Creating full path to file
            $fileName .= str_replace( $this->separator, DS, $class ) . $this->extension;
            $fileName = $this->directory . DS . $fileName;
            // If file exists - including it
            if ( is_file( $fileName ) ) {
                require_once $fileName;
            }
        }
    }

    /**
     * Register autoloader
     */
    protected function register() {
        spl_autoload_register( [ $this, 'load' ] );
    }

    /**
     * Validating namespace
     *
     * @param $namespace
     * @return false|int
     */
    protected function validateNamespace( $namespace )
    {
        //return preg_match('~^([a-zA-z0-9_]+)(' . addslashes( $this->separator ) .'[a-zA-z0-9_]+)|([a-zA-z0-9_]+)$~', $namespace);
        return preg_match('~^((?:' . addslashes( $this->separator ) .'{0,2}\w+|\w+'
            . addslashes( $this->separator ) .'{1,2})(?:\w+'
            . addslashes( $this->separator ) .'{0,2})+)$~', $namespace);
    }
}