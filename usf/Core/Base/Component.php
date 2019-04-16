<?php

namespace Usf\Core\Base;

/**
 * Class Component
 * @package Usf\Core\Base
 */
abstract class Component
{

    /**
     * Reflector for getting information about class
     * @var \ReflectionClass
     */
    protected $reflector = null;

    /**
     * Directory of class file
     * @var string
     */
    protected $directory = null;

    /**
     * Errors while processing component methods
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Get reflector
     * @return \ReflectionClass
     */
    protected function getReflector()
    {
        try {
            return $this->reflector ?? new \ReflectionClass( get_class( $this ) );
        } catch ( \ReflectionException $exception ) {
            $this->addErrorMessage( $exception->getMessage() );
        }
        return null;
    }

    /**
     * Get directory
     * @return string
     */
    protected function getDirectory()
    {
        return $this->directory ?? dirname( $this->getReflector()->getFileName() );
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
            asort( $this->errors[ $group ] );
            array_walk_recursive($this->errors[ $group ], function ( $item, $key ) use ( &$result ) {
                if ( !is_array( $item ) ) {
                    $result[] = $item;
                }
            } );
        }
        return $result;
    }

    /**
     * Clean errors
     *
     * @param string $group
     * @return $this
     */
    public function cleanErrors( $group = 'main' )
    {
        if ( array_key_exists( $group, $this->errors ) ) {
            unset( $this->errors[ $group ] );
        }
        return $this;
    }

}