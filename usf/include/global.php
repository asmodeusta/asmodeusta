<?php

use Usf\Core\Components\Database;
use Usf\Core\Components\Request;
use Usf\Core\Components\Router;
use Usf\Core\Components\Settings;
use Usf\Usf;

/**
 * Get Usf single object
 * @return Usf
 */
function usf()
{
    global $_USF;
    return $_USF;
}

/**
 * Get Database object
 * @return Database
 */
function db()
{
    global $_USF_DB;
    return $_USF_DB;
}

/**
 * Get Router object
 * @return Router
 */
function router()
{
    global $_USF_ROUTER;
    return $_USF_ROUTER;
}

/**
 * Get Request object
 * @return Request
 */
function request()
{
    global $_USF_REQUEST;
    return $_USF_REQUEST;
}

/**
 * Get Settings
 * @param string $name
 * @return Settings|mixed
 */
function settings( $name = '' )
{
    global $_USF_SETTINGS;
    if ( $name === '' ) {
        return $_USF_SETTINGS;
    } else {
        return $_USF_SETTINGS->$name;
    }
}