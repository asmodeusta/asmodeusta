<?php

class FightclubModule extends Module
{

    public function __construct($controller, $action, array $params = [])
    {
        $this->dir = __DIR__;
        parent::__construct($controller, $action, $params);
    }

    public function _authenticate()
    {

    }

    public function _logout()
    {

    }

}