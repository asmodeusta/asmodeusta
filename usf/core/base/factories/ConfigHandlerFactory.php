<?php

namespace Usf\Core\Base\Factories;

use Usf\Core\Base\Interfaces\FactoryInterface;
use Usf\Core\Base\ConfigHandler;
use Usf\Core\Base\EmptyConfigHandler;

/**
 * Class ConfigHandlerFactory
 * @package Usf\Core\Components\Factories
 */
class ConfigHandlerFactory implements FactoryInterface
{

    /**
     * @param $file
     * @return ConfigHandler
     */
    public static function create( $file )
    {
        $matches = [];
        if ( is_file( $file ) && preg_match( '~\.([\w]+)$~', $file, $matches ) ) {
            $file = realpath( $file );
            $ext = $matches[ 1 ];
            $configHandlerClassname = ucfirst( $ext ) . 'ConfigHandler';
            $configHandlerNamespace = 'Usf\Core\Components\\' . $configHandlerClassname;
            if ( class_exists( $configHandlerNamespace ) ) {
                $configHandler = new $configHandlerNamespace( $file );
                return $configHandler;
            }
        }
        return new EmptyConfigHandler( $file );
    }

}