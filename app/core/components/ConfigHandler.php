<?php

class ConfigHandler
{

    protected $section = 'site';
    protected $filename;
    protected $config = [];

    public function __construct($filename)
    {
        if(is_file($filename)) {
            $this->filename = $filename;
            $this->read();
        }
    }

    protected function read() {
        $pathinfo = pathinfo($this->filename);
        if($pathinfo['extension']==='ini') {
            $this->config = parse_ini_file($this->filename);
        }
    }

    public function getConfig() {
        return $this->config;
    }

    public function __get($name)
    {
        $result = null;
        if(isset($this->config[$name])) {
            $result = $this->config[$name];
        }
        return $result;
    }

}