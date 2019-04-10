<?php

namespace Usf\Core\Base\Traits;

/**
 * Trait Observable
 * @package Usf\Core\Base\Traits
 */
trait Observable
{

    /**
     * @var array
     */
    protected static $observers = [];

    /**
     * @param string $event
     * @param callable $observer
     * @param int $priority
     * @param int $argsNum
     * @return bool
     */
    public static function registerObserver( string $event, $observer, int $priority = 1, int $argsNum = 0 )
    {
        if ( ! array_key_exists( $event, static::$observers ) ) {
            static::$observers[ $event ] = [];
        }
        if ( ! array_key_exists( $priority, static::$observers[ $event ] ) ) {
            static::$observers[ $event ][ $priority ] = [];
        }
        $hash = md5( serialize( $observer ) );
        if ( ! array_key_exists( $hash, static::$observers[ $event ][ $priority ] ) ) {
            static::$observers[ $event ][ $priority ][ $hash ] = [
                'observer' => $observer,
                'argsNum' => $argsNum
            ];
            return true;
        }
        return false;
    }

    /**
     * @param string $event
     * @param callable $observer
     * @param int $priority
     * @return bool
     */
    public static function deleteObserver( string $event, $observer, int $priority = 1 )
    {
        if ( array_key_exists( $event, static::$observers )
            && array_key_exists( $priority, static::$observers[ $event ] ) ) {
            $hash = md5( serialize( $observer ) );
            unset( static::$observers[ $event ][ $priority ][ $hash ] );
            return true;
        }
        return false;
    }

    /**
     * @param string $event
     * @return array
     */
    protected static function handleObservers( string $event )
    {
        $args = array_shift( func_get_args() );
        if ( ! array_key_exists( $event, static::$observers ) ) {
            foreach ( static::$observers[ $event ] as $priority => $observers ) {
                foreach ( $observers as $hash => $observer) {
                    if ( is_callable( $observer[ 'observer' ] ) ) {
                        if ( $observer[ 'argsNum' ] === 0 ) {
                            $result = call_user_func_array( $observer[ 'observer' ], [] );
                        } elseif ( $observer[ 'argsNum' ] >= count( $args ) ) {
                            $result = call_user_func_array( $observer[ 'observer' ], $args );
                        } else {
                            $result = call_user_func_array( $observer[ 'observer' ], array_slice( $args, 0, $observer[ 'argsNum' ] ) );
                        }
                        array_merge_recursive( $args, $result );
                    }
                }
            }
        }
        return $args;
    }

}