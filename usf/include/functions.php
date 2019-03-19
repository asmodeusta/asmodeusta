<?php

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
 * Returns last declared class
 * @return string
 */
function lastDeclaredClass()
{
    $classes = get_declared_classes();
    return end( $classes );
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