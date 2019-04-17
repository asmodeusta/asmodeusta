<?php

namespace Usf;

use Composer\Autoload\ClassLoader;
use Usf\Base\Exceptions\SessionException;
use Usf\Base\Factories\ConfigHandlerFactory;
use Usf\Base\Factories\ModulesFactory;
use Usf\Components\Database;
use Usf\Components\Request;
use Usf\Components\Router;
use Usf\Components\Session;
use Usf\Components\Settings;

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
     * Usf start time
     *
     * @var float
     */
    private $startTime;

    /**
     * Composer autoloader
     * @var ClassLoader
     */
    private $autoloader;

    /**
     * Usf configuration file
     * @var array
     */
    private $configuration;

    /**
     * Settings
     * @var Settings
     */
    private $settings;

    /**
     * Database
     * @var Database
     */
    private $db;

    /**
     * Modules
     * @var array
     */
    private $modules = [];

    /**
     * Session
     * @var Session
     */
    private $session;

    /**
     * Router
     * @var Router
     */
    private $router;

    /**
     * Request
     * @var Request
     */
    private $request;


    /**
     * Starts the app
     *
     * @param ClassLoader $autoloader
     * @return Usf
     */
    public static function start( $autoloader = null )
    {
        return self::$usf ?? new self( $autoloader );
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
     * @param ClassLoader $autoloader
     */
    private function __construct( $autoloader )
    {
        // Start time
        $this->startTime = microtime( true );

        $this->autoloader = $autoloader;

        // Register single instance
        self::$usf = $this;
    }

    public function configure()
    {
        $configFile = DIR_CONFIG . DS . 'config.php';
        $this->configuration = ConfigHandlerFactory::create( $configFile )->getFullConfig();
        if ( ! $this->validateConfig() ) {
            // TODO: redirect to setup app
            die( 'Config error!' );
        }
        return $this;
    }

    /**
     * Initialize
     */
    public function init()
    {
        // Settings
        $this->settings = new Settings( $this->configuration[ 'settings' ] );

        // Create Database
        try {
            $this->db = new Database( $this->configuration[ 'database' ] );
        } catch ( \PDOException $exception ) {
            die('Cannot connect to database!');
        }

        // Create Router
        $this->router = new Router( $this->configuration[ 'router' ] );

        /**
         * Register Modules
         */
        $this->registerModules();

        // TODO: think where define session
        // Session
        try {
            $this->session = new Session( $this->settings->session );
        } catch ( SessionException $exception ) {
            die( '<h1>' . $exception->getMessage() . '</h1>' );
        }

        return $this;
    }

    /**
     * Run
     */
    public function run()
    {
        // Parse request
        if ( $this->router->parseRequest() ) {
            // Set request
            $this->request = $this->router->getRequest();

            // Call action
            $this->request->call();
        } else {
            // TODO: show error page
            echo 'Error! Page not found.';
        }

        return $this;
    }

    /**
     * Usf destructor
     */
    public function __destruct()
    {
        //echo '<pre>', 'Usf execution time: ', microtime( true ) - $this->startTime, ' seconds', '</pre>';
    }

    /**
     * Validating configuration
     * @return bool
     */
    private function validateConfig()
    {
        return (
            array_key_exists( 'settings', $this->configuration )
            && array_key_exists( 'database', $this->configuration )
            && array_key_exists( 'router', $this->configuration )
            && is_file( $this->configuration[ 'settings' ] )
            && is_file( $this->configuration[ 'database' ] )
            && is_file( $this->configuration[ 'router' ] )
        );
    }

    private function registerModules()
    {
        $this->modules = ModulesFactory::init( $this->settings->modules );
    }

    public function autoloader()
    {
        return $this->autoloader;
    }

    public function db()
    {
        return $this->db;
    }

    public function settings( $name = null )
    {
        return is_null($name) ? $this->settings : $this->settings->$name;
    }

    public function session()
    {
        return $this->session;
    }

    public function router()
    {
        return $this->router;
    }

    public function request()
    {
        return $this->request;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    /*********** - Object methods - ***********/
}