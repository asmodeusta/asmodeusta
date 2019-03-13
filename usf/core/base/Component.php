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
     * @throws \ReflectionException
     */
    protected function getReflector()
    {
        if ( is_null( $this->reflector ) ) {
            $this->reflector = new \ReflectionClass( get_class( $this ) );
        }
        return $this->reflector;
    }

    /**
     * Get directory
     * @return string
     * @throws \ReflectionException
     */
    protected function getDirectory()
    {
        if ( is_null( $this->directory ) ) {
            $this->directory = dirname( $this->getReflector()->getFileName() );
        }
        return $this->directory;
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