<?php

/**
 * APP INIT FILE
 */

use Usf\Usf;

/**
 * Register composer autoloader
 */
$loader = require_once 'autoload.php';

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
 * Application directory
 */
defined( 'DIR_APP' ) or define( 'DIR_APP', dirname( __DIR__ ) );

/**
 * Modules directory
 */
defined( 'DIR_MODULES' ) or define( 'DIR_MODULES', DIR_APP . DS . 'modules' );

/**
 * Themes directory
 */
defined( 'DIR_THEMES' ) or define( 'DIR_THEMES', DIR_APP . DS . 'themes' );

/**
 * Config directory
 */
defined( 'DIR_CONFIG' ) or define( 'DIR_CONFIG', DIR_APP . DS . 'config' );

/**
 * Start
 */
Usf::start( $loader )
    ->configure()
    ->init()
    ->run();
//USF::end();