<?php

/**
 * APP INIT FILE
 */

use Usf\Usf;

require_once 'Usf.php';

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
defined( 'DIR_ROOT' ) or define( 'DIR_ROOT', dirname( __FILE__ ) );

/**
 * Application directory
 */
defined( 'DIR_APP' ) or define( 'DIR_APP', dirname( __FILE__ ) );

/**
 * Framework directory
 */
defined( 'DIR_USF' ) or define( 'DIR_USF', DIR_ROOT . DS . 'usf' );

/**
 * Directory of framework core files
 */
defined( 'DIR_CORE' ) or define( 'DIR_CORE', DIR_USF . DS . 'core' );

/**
 * Modules directory
 */
defined( 'DIR_MODULES' ) or define( 'DIR_MODULES', DIR_APP . DS . 'modules' );

/**
 * Config directory
 */
defined( 'DIR_CONFIG' ) or define( 'DIR_CONFIG', DIR_APP . DS . 'config' );

/**
 * Including files in 'include' path
 */
$includeDir = DIR_USF . '/include';
ob_start();
foreach ( scandir( $includeDir ) as $file ) {
    // Check if this is php-file
    if ( preg_match( '~(\.php$)~', $file ) ) {
        require_once $includeDir . DS . $file;
    }
}
// Clean output stream
ob_clean();
/** */

/**
 * Start
 * Set global variable $USF
 */
Usf::start()->init()->run();
//USF::end();