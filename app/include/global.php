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
    return Usf::start();
}

/**
 * Get Database object
 * @return Database
 */
function db()
{
    return usf()->db();
}

/**
 * Get Router object
 * @return Router
 */
function router()
{
    return usf()->router();
}

/**
 * Get Request object
 * @return Request
 */
function request()
{
    return usf()->request();
}

/**
 * Get Settings
 * @param string $name
 * @return Settings|mixed
 */
function settings( $name = '' )
{
    return usf()->settings( $name );
}