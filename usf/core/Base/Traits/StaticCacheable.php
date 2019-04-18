<?php

namespace Usf\Base\Traits;

trait StaticCacheable
{

    protected static $staticCache = [];

    protected static function staticCache( $key = null, $data = null )
    {
        if ( is_null( $key ) ) {
            if ( is_null( $data ) ) {
                return self::$staticCache;
            } else {
                self::$staticCache = $data;
            }
        } else {
            if ( is_null( $data ) ) {
                return self::getStaticCache( $key );
            } else {
                self::setStaticCache( $key, $data );
            }
        }
        return null;
    }

    protected static function getStaticCache( $key )
    {
        return self::$staticCache[ md5( $key ) ] ?? null;
    }

    protected static function setStaticCache( $key, $data )
    {
        self::$staticCache[ md5( $key ) ] = $data;
    }

    protected static function unsetStaticCache( $key )
    {
        unset( self::$staticCache[ md5( $key ) ] );
    }

    protected static function issetStaticCache( $key )
    {
        return isset( self::$staticCache[ md5( $key ) ] );
    }

}