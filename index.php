<?php
/*
// +Debug
ini_set('display_errors',1);
error_reporting(E_ALL);
// -Debug
*/

/**
 * New site
 */

defined( 'DIR_ROOT' ) or define( 'DIR_ROOT', dirname( __FILE__ ) );

$initFile = DIR_ROOT . DIRECTORY_SEPARATOR . 'init.php';
if ( is_file( $initFile ) ) {
    $init = include $initFile;
    if ( $init[ 'app' ] && $init[ 'file' ] ) {
        require_once DIR_ROOT . DIRECTORY_SEPARATOR . $init[ 'app' ] . DIRECTORY_SEPARATOR . $init[ 'file' ];
    }
}