<?php

namespace Usf\Core\Components;

use Usf\Core\Base\Model;

class Session extends Model
{

    protected $secret = 'eUM0V3QfVNVoEPhaevsui2r6V06ole6I7wWMu4/pbrK25OILHjQk7xc3YTHF+34=';
    protected $cookieDuration = 60 * 60 * 24 * 15;

    protected $useIp = false;
    protected $useUseragent = false;

    protected $id;

    protected $token;
    protected $useragent;
    protected $ip;

    protected $time;
    protected $duration;

    protected $data = [];

    public function __construct( $token, $useragent = null, $ip = null )
    {
        $this->token = $token;
        $this->useragent = $useragent;
        $this->ip = $ip;
    }

    public function searchByToken( $token )
    {
        $result = null;
        $sql = 'select id from prefix__sessions where token = :token;';
        if ( $st = self::$db->prepare( $sql ) ) {
            $st->bindValue( ':token', $token, Database::PARAM_STR );
            if ( $res = $st->fetch( Database::FETCH_ASSOC ) ) {
                $result = $res[ 'id' ];
            }
        }
        return $result;
    }

    private function createToken()
    {
        $str = $this->secret . $this->ip . $this->useragent . $this->time;
        $hash = md5( $str );

        return $hash;
    }

    private function checkToken()
    {
        $result = ( $this->createToken() === $this->token );

        return $result;
    }

}