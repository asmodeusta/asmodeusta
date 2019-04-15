<?php

namespace Usf\Core\Base\Traits;

trait Cacheable
{

    protected static $cache = [];

    protected static function getFromCache( $id )
    {
        return static::$cache[ $id ] ?? null;
    }

    protected static function addToCache( $id, $data )
    {
        static::$cache[ $id ] = $data;
    }

    protected static function removeFromCache( $id )
    {
        unset( static::$cache[ $id ] );
    }

}