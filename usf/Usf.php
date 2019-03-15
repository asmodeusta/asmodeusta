<?php

namespace Usf;

use Usf\Core\Base\ConfigHandler;
use Usf\Core\Components\Database;
use Usf\Core\Components\Router;
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
        'router',
        'db',
        'usfStartTime'
    ];

    /**
     * Usf start time
     *
     * @var float
     */
    private $usfStartTime;

    /**
     * Language of the page
     * @var string
     */
    private $lang;

    /**
     * Class autoloader by namespaces
     *
     * @var AutoloaderNamespaces
     */
    private $autoloader;

    /**
     * Router ConfigHandler
     *
     * @var ConfigHandler
     */
    private $routerConfigHandler;

    /**
     * Router
     *
     * @var Router
     */
    private $router;

    /**
     * Database ConfigHandler
     *
     * @var ConfigHandler
     */
    private $dbConfigHandler;

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
        $this->usfStartTime = microtime( true );

        /**
         * Connect autoloader
         */
        require_once DIR_CORE . '/src/AutoloaderNamespaces.php';
        $this->autoloader = new AutoloaderNamespaces( DIR_USF, __NAMESPACE__ );

        /**
         * Create Router
         */
        $this->router = new Router();
    }

    public function __get($name)
    {
        $result = null;
        if ( in_array( $name, $this->readableProperties ) ) {
            $result = $this->$name;
        }
        return $result;
    }

    /**
     * Initialize
     */
    public function init()
    {
        /**
         * Router
         */
        $this->router->setupConfigFromFile( DIR_USF . '/config/router.config.json' );
    }

    /**
     * Run
     */
    public function run()
    {
        $this->router->parseRequest();
        if ( ! $errors = $this->router->getErrors() ) {
            $request = $this->router->getRequest();
            if ( $lang = $request->takeDataValue( 'lang' ) ) {
                $this->lang = $lang;
            }
            $request->call();
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