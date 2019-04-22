<?php

namespace Usf\Models;

use Exception;
use Usf\Base\Model;
use Usf\Components\Database;

class User extends Model
{


    protected static $current = null;

    protected $id;
    protected $name;

    public static function get($id)
    {
        try {
            $user = static::$cache[ $id ] ?? new static($id);
        } catch (Exception $exception) {
            $user = null;
        }
        return $user;
    }

    public static function exists($id)
    {
        $result = false;
        $sql = 'select count(id) id from usf_users where id = :id';
        if ($st = self::$db->prepare($sql)) {
            $st->bindValue(':id', $id, Database::PARAM_INT);
            if ($st->execute() && $queryResult = $st->fetch(Database::FETCH_ASSOC)) {
                $result = $queryResult[ 'id' ] === '1';
            }
        }
        return $result;
    }


    protected function __construct($id)
    {

    }

}