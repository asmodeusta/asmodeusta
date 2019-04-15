<?php

namespace Usf;

use Usf\Core\Base\Exceptions\SessionException;
use Usf\Core\Base\Factories\ConfigHandlerFactory;
use Usf\Core\Base\Factories\ModulesFactory;
use Usf\Core\Components\Database;
use Usf\Core\Components\Request;
use Usf\Core\Components\Router;
use Usf\Core\Components\Session;
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
     * Usf start time
     *
     * @var float
     */
    private $startTime;

    /**
     * Class autoloader by namespaces
     * @var AutoloaderNamespaces
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
     * @return Usf
     */
    public static function start()
    {
        return self::$usf ?? new self();
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
        // Start time
        $this->startTime = microtime( true );

        // Register single instance
        self::$usf = $this;

        // Connect autoloader
        require_once DIR_CORE . DS . 'src' . DS . 'AutoloaderNamespaces.php';
        $this->autoloader = new AutoloaderNamespaces( DIR_USF, __NAMESPACE__ );
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
         * Register modules
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
            echo '<pre>';
            var_dump( $this->router->getErrors() );
            echo '</pre>';
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

    public function getDb()
    {
        return $this->db;
    }

    public function getSettings( $name = null )
    {
        return is_null($name) ? $this->settings : $this->settings->$name;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    /*********** - Object methods - ***********/
}