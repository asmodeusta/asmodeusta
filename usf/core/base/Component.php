<?php

namespace Usf\Core\Base;

/**
 * Class Component
 * @package Usf\Core\Base
 */
abstract class Component
{
    /**
     * Errors while processing component methods
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Component constructor.
     */
    public function __construct()
    {

    }

    /**
     * Add error message - only internal use
     *
     * @param string $message
     * @param string $group
     * @param int $importance
     */
    protected function addErrorMessage( $message, $group = 'main', $importance = 5 )
    {
        $this->errors[ $group ][ $importance ][] = $message;
    }

    /**
     * Get all errors
     *
     * @param string $group
     * @return array|null
     */
    public function getErrors( $group = 'main' )
    {
        $result = null;
        if ( array_key_exists( $group, $this->errors ) ) {
            $result = [];
            array_walk_recursive(asort( $this->errors[ $group ] ), function ( $item, $key ) use ( &$result ) {
                if ( !is_array( $item ) ) {
                    $result[] = $item;
                }
            } );
        }
        return $result;
    }

    /**
     * Clear errors
     *
     * @param string $group
     * @return $this
     */
    public function clearErrors( $group = 'main' )
    {
        if ( array_key_exists( $group, $this->errors ) ) {
            unset( $this->errors[ $group ] );
        }
        return $this;
    }

}