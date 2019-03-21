<?php

class Db
{

    const DEFAULT_CONFIG_FILE = CORE . '/config/db.ini';

    private static $connection = null;
    private static $params = null;

    public static function getConnection() {
        $connection = null;
        if(self::$connection===null) {
            if(self::$params===null) {
                if($ch = new ConfigHandler(self::DEFAULT_CONFIG_FILE)) {
                    self::$params = $ch->getConfig();
                }
            }
            if(isset(self::$params['host'])
                &&isset(self::$params['name'])
                &&isset(self::$params['user'])
                &&isset(self::$params['pass'])) {
                $dsn = 'mysql:host='.self::$params['host'].';dbname='.self::$params['name'].'';
                $connection = new PDO($dsn, self::$params['user'], self::$params['pass']);
                $connection->exec("set names utf8");
                self::$connection = $connection;
            }
        } else {
            $connection = self::$connection;
        }
        return $connection;
    }

}