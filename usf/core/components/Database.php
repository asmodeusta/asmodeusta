<?php

namespace Usf\Core\Components;

use Usf\Core\Base\Interfaces\ConfigurableInterface;
use Usf\Core\Base\Factories\ConfigHandlerFactory;

/**
 * Class Database
 * @package Usf\Core\Components
 */
class Database implements ConfigurableInterface
{

    protected $host;
    protected $name;
    protected $user;
    protected $pass;
    protected $charset;
    protected $collate;
    protected $prefix;

    protected $connection = null;

    public function __construct(  $configFile  )
    {
        $this->setupConfigFromFile( $configFile );
    }

    public function setupConfig( array $config )
    {
        /**
         * Connection credentials
         */
        if ( array_key_exists( 'credentials', $config ) ) {
            if ( is_array( $config[ 'credentials' ] ) ) {
                $this->host = if_set( $config[ 'credentials' ][ 'host' ], '' );
                $this->name = if_set( $config[ 'credentials' ][ 'name' ], '' );
                $this->user = if_set( $config[ 'credentials' ][ 'user' ], '' );
                $this->pass = if_set( $config[ 'credentials' ][ 'pass' ], '' );
            }
        }

        /**
         * Database settings
         */
        if ( array_key_exists( 'settings', $config ) ) {
            if ( is_array( $config[ 'settings' ] ) ) {
                $this->charset = if_set( $config[ 'settings' ][ 'charset' ], '' );
                $this->collate = if_set( $config[ 'settings' ][ 'collate' ], '' );
                $this->prefix = if_set( $config[ 'settings' ][ 'prefix' ], '' );
            }
        }
    }

    public function setupConfigFromFile( $file )
    {
        $handler = ConfigHandlerFactory::create( $file );
        $this->setupConfig( $handler->getFullConfig() );
    }

    public function connect()
    {
        try {
            $result = true;
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->name . '';
            $this->connection = new \PDO( $dsn, $this->user, $this->pass );
            $this->connection->exec( "set names utf8" );
        } catch ( \PDOException $exception ) {
            $result = false;
        }
        return $result;
    }
}