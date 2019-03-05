<?php

namespace Usf;

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
    private static $usf;

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
    private $autoloaderNamespaces;


    /*********** + Static methods + ***********/

    /**
     * Starts the app
     *
     * @return Usf
     */
    public static function start()
    {
        if ( !isset( self::$usf ) ) {
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
        $this->autoloaderNamespaces = new AutoloaderNamespaces( DIR_USF, __NAMESPACE__ );
    }

    /**
     * Usf destructor
     */
    private function __destruct()
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