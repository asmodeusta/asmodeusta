<?php

namespace Usf\Base\Traits;

/**
 * Trait Observable
 * @package Usf\Core\Base\Traits
 */
trait Observable
{

    /**
     * Current events observers stack
     * @var array
     */
    protected static $eventObserversStack = [];

    /**
     * Current event listeners stack
     * @var array
     */
    protected $eventListenersStack = [];

    /**
     * List of observed class events (static)
     * [
     *     string 'className' => [
     *         string 'eventName' => [
     *             int 'priority => [
     *                 'callbacks' => [
     *                     callable observer1,
     *                     ...
     *                     callable observerN,
     *                 ]
     *                 'argsNum' => [
     *                     int argsNum1,
     *                     ...
     *                     int argsNumN,
     *                 ]
     *             ]
     *         ]
     *     ]
     * ]
     * @var array
     */
    protected static $observers = [];

    /**
     * List if listened object events (instance)
     * [
     * string 'eventName' => [
     *         int priority => [
     *             'callbacks' => [
     *                 callable listener1,
     *                 ...
     *                 callable listenerN,
     *             ]
     *             'argsNum' => [
     *                 int argsNum1,
     *                 ...
     *                 int argsNumN,
     *             ]
     *         ]
     *     ]
     * ]
     * @var array
     */
    protected $listeners = [];

    /**
     * Attaches the observer to class event
     * @param string $event
     * @param callable $observer
     * @param int $priority
     * @param int $argsNum
     * @return bool
     */
    public static function attachObserver( string $event, $observer, int $priority = 1, int $argsNum = 0 )
    {
        $class = static::class;
        if ( !array_key_exists( $class, static::$observers ) ) {
            static::$observers[ $class ] = [];
        }
        return self::attachCallback( static::$observers[ $class ], $event, $observer, $priority, $argsNum );
    }

    /**
     * Detaches the observer from event
     * @param string $event
     * @param callable $observer
     * @param int $priority
     * @return bool
     */
    public static function detachObserver( string $event, $observer, int $priority = 1 )
    {
        $class = static::class;
        if ( array_key_exists( $class, static::$observers ) ) {
            return self::detachCallback( static::$observers[ $class ], $event, $observer, $priority );
        }
        return false;
    }

    /**
     * Handles observers of event
     * @param string $event
     * @return array
     */
    protected static function handleObservers( string $event )
    {
        $args = func_get_args();
        array_shift( $args );
        $class = static::class;
        if ( !array_key_exists( $class, static::$eventObserversStack ) ) {
            static::$eventObserversStack[ $class ] = [];
        }
        if ( array_key_exists( $class, static::$observers ) ) {
            self::handleEvent( static::$observers[ $class ], $event, $args, static::$eventObserversStack[ $class ] );
        }
        return $args;
    }

    /**
     * Attaches listener to event
     * @param string $event
     * @param callable $listener
     * @param int $priority
     * @param int $argsNum
     * @return bool
     */
    public function attachListener( string $event, $listener, int $priority = 1, int $argsNum = 1 )
    {
        return self::attachCallback( $this->listeners, $event, $listener, $priority, $argsNum );
    }

    /**
     * Detaches listener from event
     * @param string $event
     * @param callable $listener
     * @param int $priority
     * @return bool
     */
    public function detachListener( string $event, $listener, int $priority = 1 )
    {
        return self::detachCallback( $this->listeners, $event, $listener, $priority );
    }

    /**
     * Handles listeners of event
     * @param string $event
     * @return array
     */
    protected function handleListeners( string $event )
    {
        $args = func_get_args();
        array_shift( $args );
        self::handleEvent( $this->listeners, $event, $args, $this->eventListenersStack, $this );
        return $args;
    }

    /**
     * Attaches callback to event list
     * @param array $eventList
     * @param string $event
     * @param callable $callback
     * @param int $priority
     * @param int $argsNum
     * @return bool
     */
    private static function attachCallback(
        array &$eventList,
        string $event,
        $callback,
        int $priority = 1,
        int $argsNum = 1
    ) {
        if ( !array_key_exists( $event, $eventList ) ) {
            $eventList[ $event ] = [];
        }
        if ( !array_key_exists( $priority, $eventList[ $event ] ) ) {
            $eventList[ $event ][ $priority ] = [ 'callback' => [], 'argsNum' => [] ];
        }
        if ( !in_array( $callback, $eventList[ $event ][ $priority ][ 'callback' ] ) ) {
            $eventList[ $event ][ $priority ][ 'callback' ][] = $callback;
            $eventList[ $event ][ $priority ][ 'argsNum' ][] = $argsNum;
            return true;
        }
        return false;
    }

    /**
     * Detaches callback from event list
     * @param array $eventList
     * @param string $event
     * @param callable $callback
     * @param int $priority
     * @return bool
     */
    private static function detachCallback( array &$eventList, string $event, $callback, int $priority = 1 )
    {
        if ( array_key_exists( $event, $eventList )
            && array_key_exists( $priority, $eventList[ $event ] ) ) {
            if ( $position = array_search( $callback, $eventList ) ) {
                unset( $eventList[ $event ][ $priority ][ 'callback' ][ $position ] );
                unset( $eventList[ $event ][ $priority ][ 'argsNum' ][ $position ] );
                return true;
            }
        }
        return false;
    }

    /**
     * Handles event
     * @param array $eventList
     * @param string $event
     * @param array $args
     * @param array $stack
     * @param null|object $subject
     */
    private static function handleEvent( array $eventList, string $event, array &$args, array &$stack, $subject = null )
    {
        // Avoiding recursive deadlock
        if ( in_array( $event, $stack ) ) {
            return;
        }
        // Register event in stack
        array_push( $stack, $event );
        // Check if event is registered
        if ( array_key_exists( $event, $eventList ) ) {
            // Sort event handlers by priority
            krsort( $eventList[ $event ], SORT_NUMERIC );
            // Map event handlers by priority
            foreach ( $eventList[ $event ] as $priority => $handlers ) {
                foreach ( $handlers[ 'callback' ] as $position => $handler ) {
                    // Get expected number of arguments
                    $argsNum = $handlers[ 'argsNum' ][ $position ];
                    if ( is_callable( $handler ) ) {
                        // Adding $subject to arguments ( if not null )
                        $calledArgs = ( is_null( $subject ) ? $args : array_merge( [ $subject ], $args ) );
                        if ( $argsNum === 0 ) {
                            $result = call_user_func_array( $handler, [] );
                        } elseif ( $argsNum >= count( $calledArgs ) ) {
                            $result = call_user_func_array( $handler, $calledArgs );
                        } else {
                            $result = call_user_func_array( $handler, array_slice( $calledArgs, 0, $argsNum ) );
                        }
                        // Adding callback result to arguments
                        $args = ( is_array( $result ) ? $result : ( is_null( $result ) ? [] : [ $result ] ) ) + $args;
                    }
                }
            }
        }
        // Unregister event from stack
        array_pop( $stack );
    }

}