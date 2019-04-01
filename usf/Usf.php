<?php

namespace Usf;

use Usf\Core\Base\Exceptions\SessionException;
use Usf\Core\Base\Factories\ConfigHandlerFactory;
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

    private const DEFAULT_SETTINGS_CONFIG_FILE = DIR_CONFIG . DS . 'settings.config.json';
    private const DEFAULT_DATABASE_CONFIG_FILE = DIR_CONFIG . DS . 'db.config.json';
    private const DEFAULT_ROUTER_CONFIG_FILE = DIR_CONFIG . DS . 'router.config.json';

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
    private $config;

    /**
     * Settings
     * @var Settings
     */
    private $settings;

    /**
     * Session
     * @var Session
     */
    private $session;

    /**
     * Database
     * @var Database
     */
    private $db;

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
        return self::$usf ?? self::$usf = new self();
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

        // Register global var
        global $_USF;
        $_USF = $this;

        // Connect autoloader
        require_once DIR_CORE . DIRECTORY_SEPARATOR . 'src' . DS . 'AutoloaderNamespaces.php';
        $this->autoloader = new AutoloaderNamespaces( DIR_USF, __NAMESPACE__ );

        // Configuration
        if ( ! $this->validateConfig() ) {
            // TODO: redirect to setup app
            die( 'Config error!' );
        }

        // Initialize USF
        $this->init();
    }

    /**
     * Initialize
     */
    private function init()
    {
        // Settings
        $this->settings = new Settings( $this->config[ 'settings' ] );
        global $_USF_SETTINGS;
        $_USF_SETTINGS = $this->settings;

        // Create Database
        try {
            $this->db = new Database( $this->config[ 'database' ] );
            global $_USF_DB;
            $_USF_DB = $this->db;
        } catch ( \PDOException $exception ) {
            die('Cannot connect to database!');
        }

        // Session
        try {
            $this->session = new Session( $this->settings->session );
            global $_USF_SESSION;
            $_USF_SESSION = $this->session;
        } catch ( SessionException $exception ) {
            die( '<h1>' . $exception->getMessage() . '</h1>' );
        }


        // Create Router
        $this->router = new Router( $this->config[ 'router' ] );
        global $_USF_ROUTER;
        $_USF_ROUTER = $this->router;

        // Run USF
        $this->run();
    }

    /**
     * Run
     */
    private function run()
    {
        // Parse request
        if ( $this->router->parseRequest() ) {
            // Set request
            $this->request = $this->router->getRequest();
            global $_USF_REQUEST;
            $_USF_REQUEST = $this->request;

            // Call action
            $this->request->call();
        } else {
            // TODO: show error page
            echo '<pre>';
            var_dump( $this->router->getErrors() );
            echo '</pre>';
        }
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
        $configFile = DIR_CONFIG . DS . 'config.php';
        $this->config = ConfigHandlerFactory::create( $configFile )->getFullConfig();
        return (
            array_key_exists( 'settings', $this->config )
            && array_key_exists( 'database', $this->config )
            && array_key_exists( 'router', $this->config )
            && is_file( $this->config[ 'settings' ] )
            && is_file( $this->config[ 'database' ] )
            && is_file( $this->config[ 'router' ] )
        );
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getSettings()
    {
        return $this->settings;
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