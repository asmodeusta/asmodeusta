<?php

namespace Usf;

use Usf\Core\Base\Exceptions\UsfException;
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

    private const DEFAULT_ROUTES_FILE = DIR_USF . '/config/routes.json';

    private const DEFAULT_DB_PARAMS_FILE = DIR_USF . '/config/db.json';

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

    private $routesFile = self::DEFAULT_ROUTES_FILE;

    private $routes;

    private $lang;


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

    /**
     * Set routes file
     * @param $file
     * @return $this
     */
    public function setRoutesFile( $file )
    {
        if ( is_file( $file ) ) {
            $this->routesFile = $file;
        }
        return $this;
    }

    public function init()
    {
        $this->routes = $this->getConfigHandler( $this->routesFile );
        $this->router
            ->addRoutes( $this->routes->setSection( 'routes' )->getConfig() )
            ->addDefaults( $this->routes->setSection( 'defaults' )->getConfig() );
    }

    /**
     * @param string $file
     * @return mixed
     * @throws UsfException
     */
    private function getConfigHandler( $file )
    {
        $matches = [];
        if ( is_file( $file ) && preg_match( '~\.([\w]+)$~', $file, $matches ) ) {
            $file = realpath( $file );
            $fileExt = $matches[ 1 ];
            $configHandlerClassname = ucfirst( $fileExt ) . 'ConfigHandler';
            $configHandlerNamespace = __NAMESPACE__ . '\Core\Components\\' . $configHandlerClassname;
            try {
                $configHandler = new $configHandlerNamespace( $file );
                return $configHandler;
            } catch( \Exception $exception ) {
                throw new UsfException( 'Class "' . $configHandlerNamespace . '" not found.' );
            }
        } else {
            throw new UsfException( 'Undefined file format: ' . $file );
        }
    }

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

    /**
     * Define basic framework constants
     */
    private function defineConstants()
    {

    }

    /*********** - Object methods - ***********/
}