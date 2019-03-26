<?php

/**
 * Get Usf single object
 * @return Usf\Usf
 */
function usf()
{
    global $_USF;
    return $_USF;
}

/**
 * Get Database object
 * @return \Usf\Core\Components\Database
 */
function db()
{
    global $_USF_DB;
    return $_USF_DB;
}

/**
 * Get Router object
 * @return \Usf\Core\Components\Router
 */
function router()
{
    global $_USF_ROUTER;
    return $_USF_ROUTER;
}

/**
 * Get Request object
 * @return \Usf\Core\Components\Request
 */
function request()
{
    global $_USF_REQUEST;
    return $_USF_REQUEST;
}

/**
 * Get Settings
 * @param string $name
 * @return \Usf\Core\Components\Settings|mixed
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