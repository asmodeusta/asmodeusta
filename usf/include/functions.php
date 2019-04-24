<?php

/**
 * Returns last declared class
 * @return string
 */
function lastDeclaredClass()
{
    $classes = get_declared_classes();
    return end($classes);
}

/**
 * Redirect
 * @param string $url
 */
function redirect($url)
{
    header('Location: ' . $url);
    die;
}

/**
 * Searches key in array, and if exists unset it, returning the value
 * @param string $needle
 * @param array $haystack
 * @return mixed|null
 */
function array_take(string $needle, array $haystack)
{
    $result = null;
    if (array_key_exists($needle, $haystack)) {
        $result = $haystack[ $needle ];
    }
    return $result;
}

/**
 * Formatting $value into 0 or 1
 * @param $value
 * @return int 0|1
 */
function format01($value)
{
    return boolval($value) ? 1 : 0;
}