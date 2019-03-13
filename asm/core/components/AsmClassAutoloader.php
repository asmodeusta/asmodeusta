<?php

namespace Core\Components;


class AsmClassAutoloader
{

    /**
     * Root path for loading
     *
     * @var string
     */
    protected $path = ROOT;

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
     * AsmClassAutoloader constructor.
     * @param string $path
     * @param string $namespace
     * @param string $separator
     * @param string $extension
     */
    public function __construct( $path, $namespace, $separator = '\\', $extension = '.php' )
    {
        $this->separator = $separator;
        $this->extension = $extension;
        if ( is_dir( $path ) ) {
            $this->path = $path;
        }
        if ( $this->validateNamespace( $namespace ) ) {
            $this->namespace = $namespace;
        }

        $this->register();

        self::$loaders[ static::class ] = $this;
    }

    /**
     * Register autoloader
     */
    protected function register() {
        spl_autoload_register( [ $this, 'load' ] );
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
                $fileName = str_replace( $this->separator, DIRECTORY_SEPARATOR, $namespace ) . ( $namespace === '' ? '' : DIRECTORY_SEPARATOR );
            }
            // Creating full path to file
            $fileName .= str_replace( $this->separator, DIRECTORY_SEPARATOR, $class ) . $this->extension;
            $fileName = $this->path . DIRECTORY_SEPARATOR . $fileName;

            // If file exists - including it
            if ( is_file( $fileName ) ) {
                require_once $fileName;
            }
        }
    }

    /**
     * Validating namespace
     *
     * @param $namespace
     * @return false|int
     */
    protected function validateNamespace( $namespace )
    {
        return preg_match('~^([a-zA-z0-9_\-]+)?(' . addslashes( $this->separator ) .'[a-zA-z0-9_\-]+)|([a-zA-z0-9_\-]+)$~', $namespace);
    }

    /**
     * DEBUGGING
     */
    public static function showLoaders() {
        echo '<pre>';
        var_dump(self::$loaders);
        echo '</pre>';
    }

}