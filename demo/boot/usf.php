<?php

/**
 * APP INIT FILE
 */

use Usf\Usf;

/**
 * Directory separator
 */
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );

/**
 * Dir constants:
 * - contains "DIR_" prefix and path name
 */

/**
 * Root directory
 */
defined( 'DIR_ROOT' ) or define( 'DIR_ROOT', dirname( __DIR__ ) );

/**
 * Usf directories
 */
defined( 'DIR_USF' ) or define( 'DIR_USF', DIR_ROOT . DS . 'usf' );
defined( 'DIR_CORE' ) or define( 'DIR_CORE', DIR_USF . DS . 'core' );

/**
 * Application directories
 */
defined( 'DIR_APP' ) or define( 'DIR_APP', dirname( __DIR__ ) );
defined( 'DIR_MODULES' ) or define( 'DIR_MODULES', DIR_APP . DS . 'modules' );
defined( 'DIR_THEMES' ) or define( 'DIR_THEMES', DIR_APP . DS . 'themes' );
defined( 'DIR_CONFIG' ) or define( 'DIR_CONFIG', DIR_APP . DS . 'config' );

/**
 * Register composer autoloader
 */
$loader = require_once 'autoload.php';

/**
 * Start
 */
Usf::start( $loader )
    ->configure()
    ->init()
    ->run();
//USF::end();