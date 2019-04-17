<?php

use Usf\Components\Database;
use Usf\Components\Request;
use Usf\Components\Router;
use Usf\Components\Settings;
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