<?php


class Session extends Model
{

    const SECRET_KEY = 'SAMBER LINDER KONOR DALEM ONORI';
    const SESSION_COOKIE_DURATION = 60 * 60 * 24 * 15;

    private static $instance;

    private static $st_t;

    private $id;
    private $token;
    private $startTime;
    private $duration;
    private $ip;
    private $agent;
    private $user = 0;
    private $status = 1;
    private $data = [];


    public static function start() {
        if(!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        //self::$st_t = microtime(true);

        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->agent = $_SERVER['HTTP_USER_AGENT'];

        $valid = false;
        if (isset($_COOKIE['asm_spt'])
            &&isset($_COOKIE['asm_sst'])
            &&isset($_COOKIE['asm_sut'])
        ) {
            $this->token = $_COOKIE['asm_spt'];
            $sec = $_COOKIE['asm_sst'];
            $usec = $_COOKIE['asm_sut'];
            $this->startTime = self::decodeMicrotime($usec, $sec);

            if($this->exists()&&$this->getData()) {
                $valid = $this->checkData();
            }
        }
        if(!$valid) {
            $this->generate();
        }
        $name = 'Session #' . $this->id;
        $this->set('name', $name);
    }

    public function __destruct()
    {
        $this->writeData();
    }

    public function __get($name) {
        $result = null;
        switch ($name) {
            case 'id':
                $result = $this->id;
                break;
            default:
                $result = $this->get($name);
                break;
        }
        return $result;
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __unset($name)
    {
        $this->unset($name);
    }

    public function get($name) {
        $result = null;
        if(isset($this->data[$name])) {
            $result = $this->data[$name];
        }
        return $result;
    }

    public function set($name, $value) {
        $this->data[$name] = $value;
    }

    public function unset($name) {
        unset($this->data[$name]);
    }

    private function generate() {
        $time = microtime();
        $code = self::codeMicrotime($time);
        $token = self::createToken($this->ip, $this->agent, $time);

        setcookie('asm_spt', $token, self::SESSION_COOKIE_DURATION + time());
        setcookie('asm_sut', $code[0], self::SESSION_COOKIE_DURATION + time());
        setcookie('asm_sst', $code[1], self::SESSION_COOKIE_DURATION + time());

        $this->token = $token;
        $this->startTime = $time;

        $this->create();
    }

    private function exists() {
        $result = false;
        $query = 'select token from session where token=:token and start_time=:start_time';
        if ($st = self::$db->prepare($query)) {
            $st->bindValue(":token", $this->token, PDO::PARAM_STR);
            $st->bindValue(":start_time", $this->startTime, PDO::PARAM_STR);

            if($r = $st->execute()&&$st->fetch()) {
                $result = true;
            }
        }
        return $result;
    }

    private function getData() {
        $result = false;
        $query = 'select id, token, start_time, duration, ip, agent, user, data, status from session where token=:token';
        if ($st = self::$db->prepare($query)) {
            $st->bindParam(":token", $this->token, PDO::PARAM_STR);
            if($st->execute()) {
                $data = $st->fetch(PDO::FETCH_ASSOC);
                $this->id = intval($data['id']);
                $this->duration = strtotime($data['duration']);
                $this->user = intval($data['user']);
                $this->status = intval($data['status']);
                $this->data = json_decode($data['data'], true);
                $result = true;
            }
        }
        return $result;
    }

    private function writeData() {
        $result = false;
        $query = 'update session set data=:data where id=:id';
        if ($st = self::$db->prepare($query)) {
            $st->bindValue(":data", json_encode($this->data), PDO::PARAM_STR);
            $st->bindValue(":id", $this->id, PDO::PARAM_INT);
            $result = $st->execute();
        }
        return $result;
    }

    private function checkData() {
        $result =   self::checkToken($this->ip, $this->agent, $this->startTime, $this->token)
                    &&$this->duration>=time()
                    &&$this->status>0;

        return $result;
    }

    private function create() {
        $result = false;
        $query = 'insert into session (token, start_time, duration, ip, agent) values(:token, :start_time, :duration, :ip, :agent)';
        if ($st = self::$db->prepare($query)) {
            $duration = date('Y-m-d H:i:s', self::SESSION_COOKIE_DURATION + time());
            $st->bindValue(":token", $this->token, PDO::PARAM_STR);
            $st->bindValue(":start_time", $this->startTime, PDO::PARAM_STR);
            $st->bindValue(":duration", $duration, PDO::PARAM_STR);
            $st->bindValue(":ip", $this->ip, PDO::PARAM_STR);
            $st->bindValue(":agent", $this->agent, PDO::PARAM_STR);
            if($st->execute()) {
                $this->id = self::$db->lastInsertId()|0;
                $result = true;
            }
        }
        return $result;
    }



    private static function createToken($ip, $agent, $time) {
        $str = self::SECRET_KEY . $ip . $agent . $time;
        /*$token = base64_encode($str);
        $hash = base64_encode(password_hash($token, PASSWORD_DEFAULT));*/
        $hash = md5($str);

        return $hash;
    }

    private static function checkToken($ip, $agent, $time, $token) {
        /*$token_confirm = base64_encode(self::SECRET_KEY . $ip . $agent . $time);
        $result = password_verify($token_confirm, base64_decode($token));*/

        $result = self::createToken($ip, $agent, $time) === $token;

        return $result;
    }

    private static function codeMicrotime($time) {
        list($usec, $sec) = explode(" ", $time);
        $usec = substr($usec, 2);
        return [
            self::mix_str($usec),
            self::mix_str($sec),
        ];
    }

    private static function decodeMicrotime($usec, $sec) {
        return '0.' . self::mix_str($usec) . ' ' . self::mix_str($sec);
    }

    private static function mix_str($str) {
        $len = strlen($str);
        $arr = [];
        for ($i = 0; $i < $len; $i++) {
            $arr[] = $i % 2 === 0 ? $str[$i] : $str[$len - $i];
        }
        return implode($arr);
    }

}