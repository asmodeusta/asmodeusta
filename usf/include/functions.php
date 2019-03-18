<?php

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
 * @return \Usf\Core\Components\Database
 */
function db()
{
    return usf()->db;
}

/**
 * If var is null returns default value
 * @param mixed $var
 * @param mixed $default
 * @return mixed
 */
function if_null( $var, $default )
{
    return is_null( $var ) ? $default : $var;
}

/**
 * If var is null returns default value
 * @param mixed $var
 * @param mixed $default
 * @return mixed
 */
function if_set( $var, $default  )
{
    return isset( $var ) ? $var : $default;
}

/**
 * Redirect
 * @param string $url
 */
function redirect( $url )
{
    header( 'Location: ' . $url );
    die;
}