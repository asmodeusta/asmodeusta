<?php

use Usf\Usf;
require_once 'Usf.php';

/**
 * Directory separator
 */
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );

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
defined( 'DIR_CORE' ) or define( 'DIR_CORE', DIR_USF . DS . 'core' );

/**
 * Modules directory
 */
defined( 'DIR_MODULES' ) or define( 'DIR_MODULES', DIR_USF . DS . 'modules' );

/**
 * Including files in 'include' path
 */
$includeDir = DIR_USF . '/include';
foreach ( scandir( $includeDir ) as $file ) {
    ob_start();
    // Check if this is php-file
    if ( preg_match( '~(\.php$)~', $file ) ) {
        require_once $includeDir . DS . $file;
    }
    // Clean output stream
    ob_clean();
}
/** */

/**
 * Start
 * Set global variable $USF
 */
Usf::start();
USF::end();