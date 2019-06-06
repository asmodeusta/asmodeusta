<?php

namespace Usf\Base\Traits;

/**
 * Trait DataTrait
 * @package Usf\Base\Traits
 */
trait DataTrait
{

    /**
     * @var array Associative array of saved data pairs $key => $value
     */
    protected $data = [];

    /**
     * Universal method to get or set data
     * @param string $key
     * @param mixed $value
     * @return mixed|void
     */
    public function data(string $key = null, $value = null)
    {
        $callback = 'readData';
        $numArgs = 0;
        if (!is_null($key)) {
            $numArgs++;
            if (is_null($value)) {
                $callback = 'getData';
            } else {
                $numArgs++;
                $callback = 'setData';
                $this->setData($key, $value);
            }
        }
        return call_user_func_array([$this, $callback], array_slice([$key, $value], 0, $numArgs));
    }

    /**
     * Set data value by key
     * @param string $key
     * @param mixed $value
     */
    public function setData(string $key, $value) : void
    {
        $this->data[$key] = $value;
    }

    /**
     * Get data value by key
     * @param string $key
     * @return mixed|null
     */
    public function getData(string $key)
    {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }

    /**
     * Unset data value by key
     * @param string $key
     */
    public function unsetData(string $key) : void
    {
        unset($this->data[$key]);
    }

    /**
     * Add portion of data
     * @param array $data
     */
    public function addData(array $data) : void
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Get all data values as array
     * @return array
     */
    public function readData() : array
    {
        return $this->data;
    }

    /**
     * Unset all data values
     */
    public function clearData()
    {
        $this->data = [];
    }

}