<?php

namespace Core\Base;

use Core\Base\Interfaces\ModuleInterface;

class Module implements ModuleInterface
{
    protected $reflector;

    public function __construct()
    {
        $this->reflector = new \ReflectionClass( get_class( $this ) );
    }

    public function getController( $name )
    {

    }

    public function getFile()
    {
        return $this->reflector->getFileName();
    }


}