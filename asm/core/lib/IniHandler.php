<?php

namespace Core\Lib;

/**
 * Class IniHandler
 * @package Asm\Components
 */
class IniHandler
{

    /**
     * Section for processing
     *
     * @var null|string
     */
    protected $section = null;

    /**
     * Name of ini file
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
     * IniHandler constructor.
     * @param string $filename
     * @param string|null $section
     */
    public function __construct($filename, $section = null)
    {
        if (is_file($filename)) {
            $this->filename = $filename;
            $this->section = $section;
            $this->read();
        }
    }

    /**
     * Reading data from ini-file
     */
    protected function read() {
        $pathInfo = pathinfo($this->filename);
        if ($pathInfo['extension'] === 'ini') {
            $data = parse_ini_file($this->filename, true);
            if (isset($this->section) && isset($data[$this->section])) {
                $data = $data[$this->section];
            }
            $this->data = $data;
        }
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

}