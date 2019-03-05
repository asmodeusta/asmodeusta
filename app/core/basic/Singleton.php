<?php

class Singleton
{

    protected static $instance;

    final public static function go() {
        if(!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    protected function __construct() {

    }

    private final function __clone() {}
    private final function __wakeup() {}

}