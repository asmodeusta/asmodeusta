<?php

namespace Core\Lib;

/**
 * Class JsonHandler
 * @package Asm\Components
 */
class JsonHandler
{

    /**
     * Name of json-file
     *
     * @var string
     */
    protected $filename;

    /**
     * Data red from file
     *
     * @var array
     */
    protected $data = [];

    /**
     * JsonHandler constructor.
     * @param $filename
     * @param bool $getFirst
     */
    public function __construct( $filename, $getFirst = true )
    {
        if (is_file($filename)) {
            $this->filename = $filename;
            $this->read($getFirst);
        }
    }

    /**
     * Reading data from json-file
     *
     * @param bool $getFirst
     */
    private function read($getFirst = true)
    {
        $pathInfo = pathinfo($this->filename);
        if ($pathInfo['extension'] === 'json') {
            $json = file_get_contents($this->filename);
            $data = json_decode($json, false, 512, JSON_BIGINT_AS_STRING);
            if ($getFirst && isset($data[0])) {
                $data = $data[0];
            }
            $this->data = $data;
        }
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

}