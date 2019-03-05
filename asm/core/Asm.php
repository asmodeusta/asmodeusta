<?php

namespace Asm;


class Asm
{

    private $settings = [];

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // Settings


    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    private function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    private function __sleep()
    {
        // TODO: Implement __sleep() method.
    }

    private function __wakeup()
    {
        // TODO: Implement __wakeup() method.
    }


}

function Asm() {
    return Asm::instance();
}