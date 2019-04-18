<?php

namespace Usf\Base\Factories;

use Usf\Base\Interfaces\StaticFactoryInterface;
use Usf\Base\ConfigHandler;
use Usf\Base\EmptyConfigHandler;

/**
 * Class ConfigHandlerFactory
 * @package Usf\Core\Components\Factories
 */
class ConfigHandlerStaticFactory implements StaticFactoryInterface
{
    /**
     * List of recently created ConfigHandlers
     * @var array
     */
    protected static $list = [];

    /**
     * List of namespaces of available config handlers
     * @var array
     */
    protected static $namespaces = [
        'Usf\\Components\\ConfigHandlers\\',
    ];

    /**
     * @param string $file - path to config file
     * @param string $section - main config section
     * @return ConfigHandler
     */
    public static function create($file, $section = null)
    {
        $handler = null;
        $matches = [];
        if (preg_match('~\.([\w]+)$~', $file, $matches)) {
            if (is_file($file)) {
                $file = realpath($file);
            }
            if (array_key_exists($file, static::$list)) {
                $handler = static::$list[$file];
            } else {
                $ext = $matches[1];
                $configHandlerClassname = ucfirst($ext) . 'ConfigHandler';
                foreach (static::$namespaces as $namespace) {
                    $configHandlerNamespace = $namespace . $configHandlerClassname;
                    if (class_exists($configHandlerNamespace)) {
                        $handler = new $configHandlerNamespace($file);
                        break;
                    }
                }

            }
        }
        if (is_null($handler)) {
            $handler = new EmptyConfigHandler($file);
        }
        static::$list[$file] = $handler;
        return $handler;
    }

    /**
     * Add config handler namespace to the namespaces array
     * @param string $namespace
     */
    public static function addNamespace($namespace)
    {
        if (!in_array($namespace, static::$namespaces)) {
            static::$namespaces[] = $namespace;
        }
    }

}