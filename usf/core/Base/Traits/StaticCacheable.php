<?php

namespace Usf\Base\Traits;

/**
 * Trait StaticCacheable
 * @package Usf\Base\Traits
 */
trait StaticCacheable
{

    /**
     * Cache array
     * @var array
     */
    protected static $staticCache = [];

    /**
     * @param mixed|null $key
     * @param mixed|null $data
     * @return array|null
     */
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

    /**
     * @param $key
     * @return mixed|null
     */
    protected static function getStaticCache( $key )
    {
        return self::$staticCache[ md5( $key ) ] ?? null;
    }

    /**
     * @param mixed $key
     * @param mixed $data
     * @return mixed
     */
    protected static function setStaticCache( $key, $data )
    {
        return self::$staticCache[ md5( $key ) ] = $data;
    }

    /**
     * @param mixed $key
     */
    protected static function unsetStaticCache( $key )
    {
        unset( self::$staticCache[ md5( $key ) ] );
    }

    /**
     * @param $key
     * @return bool
     */
    protected static function issetStaticCache( $key )
    {
        return isset( self::$staticCache[ md5( $key ) ] );
    }

}