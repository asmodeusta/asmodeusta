<?php

namespace Usf\Core\Base;

/**
 * Class DataStorage
 * @package Usf\Core\Base
 */
class DataStorage
{

    protected $data = [];

    /**
     * DataStorage constructor.
     * @param array $data
     */
    public function __construct( array $data )
    {
        $this->setData( $data );
    }

    /**
     * Data getter
     * @param string $name
     */
    public function __get( $name )
    {
        if_set( $this->data[ $name ], null );
    }

    /**
     * Data setter
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name, $value )
    {
        $this->data[ $name ] = $value;
    }

    /**
     * Data cleaner
     * @param string $name
     */
    public function __unset( $name )
    {
        unset( $this->data[ $name ] );
    }

    /**
     * Set data
     * @param array $data
     */
    public function setData( array $data )
    {
        $this->data += $data;
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Clear data
     */
    public function clearData()
    {
        $this->data = [];
    }

}