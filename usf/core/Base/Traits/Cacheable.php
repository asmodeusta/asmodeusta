<?php

namespace Usf\Base\Traits;

trait Cacheable
{

    protected $cache = [];

    protected function cache( $key = null, $data = null )
    {
        if ( is_null( $key ) ) {
            if ( is_null( $data ) ) {
                return $this->cache;
            } else {
                $this->cache = $data;
            }
        } else {
            if ( is_null( $data ) ) {
                return $this->getCache( $key );
            } else {
                $this->setCache( $key, $data );
            }
        }
        return null;
    }

    protected function getCache( $key )
    {
        return $this->cache[ md5( $key ) ] ?? null;
    }

    protected function setCache( $key, $data )
    {
        $this->cache[ md5( $key ) ] = $data;
    }

    protected function unsetCache( $key )
    {
        unset( $this->cache[ md5( $key ) ] );
    }

    protected function issetCache( $key )
    {
        return isset( $this->cache[ md5( $key ) ] );
    }
    
}