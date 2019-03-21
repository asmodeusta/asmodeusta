<?php


class Model
{
    protected static $db = null;

    public static function init() {
        if(self::$db===null) {
            self::$db = Db::getConnection();
        }
    }

}

Model::init();