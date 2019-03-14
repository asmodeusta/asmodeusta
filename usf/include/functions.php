<?php

/**
 * If var is null returns default value
 * @param mixed $var
 * @param mixed $default
 * @return null
 */
function if_null( $var, $default )
{
    return is_null( $var ) ? $default : $var;
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