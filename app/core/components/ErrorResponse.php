<?php

class ErrorResponse
{

    private $list = null;
    private $code;

    public function __construct($parameter, $code = null) {
        if(is_array($parameter)) {
            $this->list = $parameter;
        } elseif (is_string($parameter)) {
            $this->list[] = $parameter;
        }
        if(isset($code)) {
            $this->code = $code;
        }
    }

    public function __get($name)
    {
        $result = null;
        if($name === 'code') {
            $result = $this->code;
        } elseif(is_array($this->list)) {
            if($name === 'text') {
                $result = array_shift($this->list);
            } elseif ($name = 'list') {
                $result = $this->list;
            }
        }
        return $result;
    }

}