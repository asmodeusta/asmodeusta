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
 * @param mixed $value
 * @return int 0|1
 */
function format01($value) : int
{
    return boolval($value) ? 1 : 0;
}

/**
 * `str_split` for unicode
 * @param string $str
 * @return array
 */
function mb_str_split(string $str) : array
{
    preg_match_all('~.{1}~uis', $str, $out);
    return $out[ 0 ];
}

/**
 * Get the factorial of the number
 * @param int $number
 * @return int
 */
function factorial(int $number) : int
{
    return $number <= 1 ? $number : $number * factorial($number - 1);
}

