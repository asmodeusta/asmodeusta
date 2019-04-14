<?php

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