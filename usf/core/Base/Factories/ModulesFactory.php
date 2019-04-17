<?php


namespace Usf\Base\Factories;

use Usf\Base\Interfaces\FactoryInterface;

class ModulesFactory implements FactoryInterface
{

    protected static $modules = [];

    public static function init( $modules )
    {
        foreach ( $modules as $module ) {
            $moduleClassName = ucfirst( $module ) . 'Module';
            $moduleFile = DIR_MODULES . DS . $module . DS . $moduleClassName . '.php';
            if ( is_file( $moduleFile ) ) {
                include_once $moduleFile;
                $moduleClass = lastDeclaredClass();
                static::$modules[ $module ] = $moduleClass;
            }
        }
        return static::$modules;
    }

    public static function create( $name )
    {
        $moduleClassName = null;
        if ( array_key_exists( $name, static::$modules ) ) {
            $moduleClassName = static::$modules[ $name ];
        }
        return $moduleClassName;
    }


}