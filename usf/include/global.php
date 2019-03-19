<?php

/**
 * Get Usf single object
 * @return Usf
 */
function usf()
{
    global $USF;
    return $USF;
}

/**
 * Get Database object
 * @return \Usf\Core\Components\Database
 */
function db()
{
    global $DB;
    return $DB;
}

/**
 * Get Router object
 * @return \Usf\Core\Components\Router
 */
function router()
{
    global $ROUTER;
    return $ROUTER;
}

/**
 * Get Request object
 * @return \Usf\Core\Components\Request
 */
function request()
{
    global $REQUEST;
    return $REQUEST;
}