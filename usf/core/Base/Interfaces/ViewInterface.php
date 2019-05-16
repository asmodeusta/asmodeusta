<?php


namespace Usf\Base\Interfaces;


interface ViewInterface
{

    /**
     * View constructor.
     * @param string|null $template Real path to template file
     * @param array|null $data Data
     */
    public function __construct(string $template = null, array $data = null);

    /**
     * Renders view
     */
    public function render() : void;

    /**
     * Set template file
     * @param string $template Real path to template file
     * @return bool
     */
    public function setTemplate(string $template) : bool;

    /**
     * Set data value
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * Set data
     * @param array $data
     * @return bool
     */
    public function setData(array $data) : bool;

    /**
     * Add data
     * @param array $data
     * @return bool
     */
    public function addData(array $data) : bool;

    /**
     * Get data
     * @return array
     */
    public function getData() : array ;

    /**
     * Get data value
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

}