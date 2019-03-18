<?php

namespace Usf;

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
        'usfStartTime',
        'lang'
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
     * Router
     *
     * @var Router
     */
    private $router;

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
        require_once DIR_CORE . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'AutoloaderNamespaces.php';
        $this->autoloader = new AutoloaderNamespaces( DIR_USF, __NAMESPACE__ );

        /**
         * Create Router
         */
        $this->router = new Router( DIR_USF . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'router.config.json' );

        /**
         * Create Database
         */
        try {
            $this->db = new Database( DIR_USF . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'db.config.json' );
        } catch ( \PDOException $exception ) {
            die('Cannot connect to database!');
        }
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