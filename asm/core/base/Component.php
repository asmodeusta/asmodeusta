<?php

namespace Core\Base;

/**
 * Class Component
 * @package Core\Base
 */
class Component
{
    /**
     * Errors while processing component methods
     *
     * @var array
     */
    protected $errors = [];

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

}