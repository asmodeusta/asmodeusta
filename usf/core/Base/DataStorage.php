<?php

namespace Usf\Base;

/**
 * Class DataStorage
 * @package Usf\Core\Base
 */
class DataStorage
{

    /**
     * Data array
     * @var array
     */
    protected $data = [];

    /**
     * DataStorage constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setData($data);
    }

    /**
     * Data getter
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->data[ $name ] ?? null;
    }

    /**
     * Data setter
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[ $name ] = $value;
    }

    /**
     * Data cleaner
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->data[ $name ]);
    }

    /**
     * Set data
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Add data
     * @param array $data
     */
    public function addData(array $data)
    {
        $this->data += array_merge($this->data, $data);
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