<?php

namespace Usf\Components;

use PDO;
use PDOStatement;
use Usf\Base\Traits\Configurable;

/**
 * Class Database abstraction over PDO
 * @package Usf\Core\Components
 */
class Database extends PDO
{

    use Configurable;

    /**
     * Host
     * @var string
     */
    protected $host;

    /**
     * Port
     * @var int
     */
    protected $port;

    /**
     * Database name
     * @var string
     */
    protected $name;

    /**
     * Database user
     * @var string
     */
    protected $user;

    /**
     * Database password
     * @var string
     */
    protected $pass;

    /**
     * Charset
     * @var string
     */
    protected $charset;

    /**
     * Collate
     * @var string
     */
    protected $collate;

    /**
     * Table prefix
     * @var string
     */
    protected $prefix = "usf_";

    /**
     * Is connected
     * @var bool
     */
    protected $connected = false;

    /**
     * Database constructor.
     * @param string $configFile
     */
    public function __construct($configFile)
    {
        $this->setConfigFile($configFile)->configure()->setup();
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s', $this->host, $this->port, $this->name);
        parent::__construct($dsn, $this->user, $this->pass);
    }

    protected function setup()
    {
        $this->setupConfig($this->configuration);
    }

    /**
     * @param array $config
     */
    public function setupConfig(array $config)
    {
        if (!$this->connected) {

            /**
             * Connection credentials
             */
            if (array_key_exists('credentials', $config)) {
                if (is_array($config[ 'credentials' ])) {
                    $this->host = $config[ 'credentials' ][ 'host' ] ?? '';
                    $this->port = $config[ 'credentials' ][ 'port' ] ?? '';
                    $this->name = $config[ 'credentials' ][ 'name' ] ?? '';
                    $this->user = $config[ 'credentials' ][ 'user' ] ?? '';
                    $this->pass = $config[ 'credentials' ][ 'pass' ] ?? '';
                }
            }

            /**
             * Database settings
             */
            if (array_key_exists('settings', $config)) {
                if (is_array($config[ 'settings' ])) {
                    $this->charset = $config[ 'settings' ][ 'charset' ] ?? '';
                    $this->collate = $config[ 'settings' ][ 'collate' ] ?? '';
                    $this->prefix = $config[ 'settings' ][ 'prefix' ] ?? '';
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $statement
     */
    protected function fillTablePrefixes(&$statement)
    {
        $statement = str_replace('usf_', $this->prefix, $statement);
    }

    /**
     * @param string $statement
     * @param null $driver_options
     * @return bool|PDOStatement
     */
    public function prepare($statement, $driver_options = null)
    {
        $this->fillTablePrefixes($statement);
        return parent::prepare($statement, []);
    }

    /**
     * @param string $statement
     * @return int
     */
    public function exec($statement)
    {
        $this->fillTablePrefixes($statement);
        return parent::exec($statement);
    }

    /**
     * @param string $statement
     * @param int $mode
     * @param null $arg3
     * @param array $ctorargs
     * @return false|PDOStatement
     */
    public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        $this->fillTablePrefixes($statement);
        return parent::query($statement, $mode, $arg3, $ctorargs);
    }

}