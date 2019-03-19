<?php

namespace Usf;

use Usf\Core\Components\Database;
use Usf\Core\Components\Request;
use Usf\Core\Components\Router;
use Usf\Core\Components\Settings;
use Usf\Core\Src\AutoloaderNamespaces;

/**
 * Class Usf
 * Main class of the app
 *
 * @package Usf
 */
final class Usf
{

    /**
     * Single instance of the class
     *
     * @var Usf
     */
    private static $usf = null;

    /**
     * Array of protected properties for reading
     * @var array
     */
    private $readableProperties = [
        'settings',
        'router',
        'request',
        'db',
        'startTime'
    ];

    /**
     * Usf start time
     *
     * @var float
     */
    private $startTime;

    /**
     * Usf configuration file
     * @var array
     */
    private $config;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * Class autoloader by namespaces
     *
     * @var AutoloaderNamespaces
     */
    private $autoloader;

    /**
     * Router
     *
     * @var Router
     */
    private $router;

    /**
     * Request
     *
     * @var Request
     */
    private $request;

    /**
     * Database
     *
     * @var Database
     */
    private $db;


    /*********** + Static methods + ***********/

    /**
     * Starts the app
     *
     * @return Usf
     */
    public static function start()
    {
        if ( is_null( self::$usf ) ) {
            self::$usf= new self();
        }
        return self::$usf;
    }

    /**
     * Stops the app
     */
    public static function end()
    {
        self::$usf = null;
    }

    /*********** - Static methods - ***********/


    /*********** + Object methods + ***********/

    /**
     * Usf constructor.
     */
    private function __construct()
    {
        /**
         * Start time
         */
        $this->startTime = microtime( true );

        /**
         * Configuration
         */
        if ( ! $this->validateConfig() ) {
            die( 'Config error!' );
        }

        /**
         * Connect autoloader
         */
        require_once DIR_CORE . DIRECTORY_SEPARATOR . 'src' . DS . 'AutoloaderNamespaces.php';
        $this->autoloader = new AutoloaderNamespaces( DIR_USF, __NAMESPACE__ );

    }

    public function __get($name)
    {
        $result = null;
        if ( in_array( $name, $this->readableProperties ) ) {
            $result = $this->$name;
        }
        return $result;
    }

    private function validateConfig()
    {
        $configFile = DIR_USF . DS . 'config' . DS . 'config.php';
        if ( is_file( $configFile ) ) {
            $this->config = include $configFile;
            return array_key_exists( 'settings', $this->config )
                && array_key_exists( 'router', $this->config )
                && array_key_exists( 'database', $this->config )
                && is_file( $this->config[ 'settings' ] )
                && is_file( $this->config[ 'router' ] )
                && is_file( $this->config[ 'database' ] );
        }
        return false;
    }

    /**
     * Initialize
     */
    public function init()
    {
        /**
         * Settings
         */
        $this->settings = new Settings( $this->config[ 'settings' ] );

        /**
         * Create Router
         */
        $this->router = new Router( $this->config[ 'router' ] );
        global $ROUTER;
        $ROUTER = $this->router;

        /**
         * Create Database
         */
        try {
            $this->db = new Database( $this->config[ 'database' ] );
            global $DB;
            $DB = $this->db;
        } catch ( \PDOException $exception ) {
            die('Cannot connect to database!');
        }
    }

    /**
     * Run
     */
    public function run()
    {
        $this->router->parseRequest();
        if ( ! $errors = $this->router->getErrors() ) {
            $this->request = $this->router->getRequest();
            global $REQUEST;
            $REQUEST = $this->request;
            $this->request->call();
        } else {
            echo '<pre>';
            var_dump($errors);
            echo '</pre>';
        }
    }

    /**
     * Usf destructor
     */
    public function __destruct()
    {
        //echo '<pre>', 'Usf time: ', microtime( true ) - $this->usfStartTime, ' seconds', '</pre>';
    }

    /*********** - Object methods - ***********/
}