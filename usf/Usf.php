<?php

namespace Usf;

use Usf\Core\Components\Router;
use Usf\Core\Src\AutoloaderNamespaces;

/**
 * Class Usf
 * Main class of the app
 *
 * @package Usf
 */
class Usf
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
    private $usfStartTime;

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

    private $defaults;


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
    public static function stop()
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
        $this->usfStartTime = microtime( true );

        /**
         * First define basic constants of the app
         */
        $this->defineConstants();

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

    public function init()
    {
        $routesJson = file_get_contents( DIR_USF . '/config/routes.json' );
        $routes = json_decode( $routesJson, true, 512, JSON_BIGINT_AS_STRING );
        $defaults = [
            'language' => 'en',
            'module' => 'site',
            'controller' => 'main',
            'action' => 'index'
        ];
        $this->router->addRoutes( $routes )->setDefaults( $defaults );

    }

    public function run()
    {
        echo '<pre>';
        $this->router->parseRequest();
        if ( ! $errors = $this->router->getErrors() ) {
            $request = $this->router->getRequest();
            $request->call();
            var_dump( $request );
        } else {
            var_dump( $errors );
        }
        echo '</pre>';
    }

    /**
     * Usf destructor
     */
    public function __destruct()
    {
        echo '<pre>', 'Usf time: ', microtime( true ) - $this->usfStartTime, ' seconds', '</pre>';
    }

    /**
     * Define basic framework constants
     */
    private function defineConstants()
    {
        /**
         * Dir constants:
         * - consists of "DIR_"-prefix and name of important part of te application
         */

        /**
         * Root directory of the site
         */
        defined( 'DIR_ROOT' ) or define( 'DIR_ROOT', dirname( __DIR__ ) );

        /**
         * Framework directory
         */
        defined( 'DIR_USF' ) or define( 'DIR_USF', dirname( __FILE__ ) );

        /**
         * Directory of framework core files
         */
        defined( 'DIR_CORE' ) or define( 'DIR_CORE', DIR_USF . '/core' );

        /**
         * Modules directory
         */
        defined( 'DIR_MODULES' ) or define( 'DIR_MODULES', DIR_USF . '/modules' );

    }

    /*********** - Object methods - ***********/
}