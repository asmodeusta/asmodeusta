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
     * @param string $file - path to config file
     * @param string $section - main config section
     * @return ConfigHandler
     */
    public static function create( $file, $section = null )
    {
        $matches = [];
        if ( preg_match( '~\.([\w]+)$~', $file, $matches ) ) {
            if ( is_file( $file ) ) {
                $file = realpath( $file );
            }
            $ext = $matches[ 1 ];
            $configHandlerClassname = ucfirst( $ext ) . 'ConfigHandler';
            $configHandlerNamespace = 'Usf\Core\Components\ConfigHandlers\\' . $configHandlerClassname;
            if ( class_exists( $configHandlerNamespace ) ) {
                return new $configHandlerNamespace( $file );
            }
        }
        return new EmptyConfigHandler( $file );
    }

}