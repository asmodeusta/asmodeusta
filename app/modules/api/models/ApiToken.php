<?php

class ApiToken extends Model
{

    const DURATION_CODE = 1*60*60;
    const DURATION_REFRESH = 10*60;

    public function __construct()
    {

    }


    /* *** Model methods *** */
    public static function exists($code)
    {
        $result = false;
        $query = 'select _id from token where code=:code';
        if ($st = self::$db->prepare($query)) {
            $st->bindParam(":code", $code, PDO::PARAM_INT);
            $result = ($st->execute() && $st->fetch());
        }
        return $result;
    }

    private static function create()
    {
        $result = false;
        $query = 'insert into token(code, refresh, created_at, expired_at, expired_refresh, user) 
                values(:code, :refresh, :created_at, :expired_at, :expired_refresh, :user)';
        if ($st = self::$db->prepare($query)) {
            $code = self::generateToken();
            $refresh = self::generateToken();
            $createdAt = time();
            $expiredAt = $createdAt + self::DURATION_CODE;
            $expiredRefresh = $expiredAt + self::DURATION_REFRESH;
            $user = App()->user->_id;

            $st->bindValue(":code", $code, PDO::PARAM_STR);
            $st->bindValue(":refresh", $refresh, PDO::PARAM_STR);
            $st->bindValue(":created_at", $createdAt, PDO::PARAM_STR);
            $st->bindValue(":expired_at", $expiredAt, PDO::PARAM_STR);
            $st->bindValue(":expired_refresh", $expiredRefresh, PDO::PARAM_STR);
            $st->bindValue(":user", $user, PDO::PARAM_INT);
            if($st->execute()) {
                $id = self::$db->lastInsertId();
                $result = new self($id);
            }
        }
        return $result;
    }

    private static function generateToken() {
        $uid = uniqid();
        $time = microtime(true);
        return md5($uid . strval($time));
    }

}