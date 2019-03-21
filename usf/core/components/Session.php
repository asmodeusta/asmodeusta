<?php

namespace Usf\Core\Components;

use Usf\Core\Base\DbComponent;

/**
 * Class Session
 * @package Usf\Core\Components
 */
class Session extends DbComponent
{
    protected const COOKIE_NAME = 'usf_slt';

    protected $secret = 'this_is_standard_session_salt';
    protected $duration = 60 * 60 * 24 * 5;
    protected $entropy = 1;
    protected $extend = true;

    protected $use = [];

    protected $id;

    protected $token;
    protected $useragent;
    protected $ip;

    protected $startTime;
    protected $endTime;

    protected $user = 0;

    protected $data = [];
    protected $modified = false;

    /**
     * Session constructor.
     * @param array $settings
     */
    public function __construct( array $settings = [] )
    {
        parent::__construct();
        // Settings
        if ( $settings !== [] ) {
            array_key_exists( 'secret', $settings ) ? $this->secret = $settings[ 'secret' ] : null;
            array_key_exists( 'duration', $settings ) ? $this->duration = $settings[ 'duration' ] : null;
            array_key_exists( 'entropy', $settings ) ? $this->entropy = $settings[ 'entropy' ] : null;
            array_key_exists( 'extend', $settings ) ? $this->extend = $settings[ 'extend' ] : null;
            if ( array_key_exists( 'use', $settings ) && is_array( $settings[ 'use' ] ) ) {
                $this->use = $settings[ 'use' ];
            }
        }
        // Verifications
        // Check cookie
        if ( array_key_exists( self::COOKIE_NAME, $_COOKIE ) ) {
            $this->token = $_COOKIE[ self::COOKIE_NAME ];
            // Read session from db
            if ( $this->read() ) {
                $result = true;
                // Useragent verification
                if ( array_key_exists( 'useragent', $this->use ) && $this->use[ 'useragent' ] ) {
                    $result = $this->useragent === $_SERVER[ 'HTTP_USER_AGENT' ];
                } else {
                    if ( $this->useragent !== $_SERVER[ 'HTTP_USER_AGENT' ] ) {
                        $this->useragent = $_SERVER[ 'HTTP_USER_AGENT' ];
                        $this->modified = true;
                    }
                }
                // IP verification
                if ( $result && array_key_exists( 'ip', $this->use ) && $this->use[ 'ip' ] ) {
                    $result = $this->ip === $_SERVER['REMOTE_ADDR'];
                } else {
                    if ( $this->ip !== $_SERVER[ 'REMOTE_ADDR' ] ) {
                        $this->ip = $_SERVER[ 'REMOTE_ADDR' ];
                        $this->modified = true;
                    }
                }
                // Extend session duration
                if ( $this->extend ) {
                    $this->endTime = time() + $this->duration;
                    $this->modified = true;
                }
                // If all verifications complete return
                if ( $result ) {
                    return;
                }
            }
        }
        // If session not verified
        // Create new session
        if ( ! $this->new() ) {
            die( 'Cannot start session' );
        }
    }

    /**
     * Get data var
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        return $this->get( $name );
    }

    /**
     * Set data var
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name, $value )
    {
        $this->set( $name, $value );
    }

    /**
     * Unset data var
     * @param string $name
     */
    public function __unset( $name )
    {
        $this->unset( $name );
    }

    /**
     * Get data var
     * @param string $name
     * @return mixed
     */
    public function get( $name )
    {
        return if_set( $this->data[ $name ], null );
    }

    /**
     * Set data var
     * @param string $name
     * @param mixed $value
     */
    public function set( $name, $value )
    {
        if ( array_key_exists( $name, $this->data ) && $this->data[ $name ] !== $value ) {
            $this->data[ $name ] = $value;
            $this->modified = true;
        }
    }

    /**
     * Unset data var
     * @param string $name
     */
    public function unset( $name )
    {
        if ( array_key_exists( $name, $this->data ) ) {
            unset( $this->data[ $name ] );
            $this->modified = true;
        }
    }

    /**
     * Session destructor
     */
    public function __destruct()
    {
        if ( $this->modified ) {
            // Save data if modified
            $this->saveData();
        }
    }

    /**
     * Start new session
     * @return bool
     */
    protected function new()
    {
        $this->token = $this->createToken();
        $this->useragent = $_SERVER[ 'HTTP_USER_AGENT' ];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->startTime = time();
        $this->endTime = $this->startTime + $this->duration;
        $this->data = [];
        // Save to database
        $sql = 'insert into usf_sessions( token, useragent, ip, start_time, end_time, data )
            values( :token, :useragent, :ip, from_unixtime(:startTime), from_unixtime(:endTime), :data );';
        $this->db->beginTransaction();
        if ( $st = $this->db->prepare( $sql ) ) {
            $st->bindValue( ':token', $this->token, Database::PARAM_STR );
            $st->bindValue( ':useragent', $this->useragent, Database::PARAM_STR );
            $st->bindValue( ':ip', $this->ip, Database::PARAM_STR );
            $st->bindValue( ':startTime', $this->startTime, Database::PARAM_INT );
            $st->bindValue( ':endTime', $this->endTime, Database::PARAM_INT );
            $st->bindValue( ':data', serialize($this->data), Database::PARAM_STR );
            if ( $queryResult = $st->execute() ) {
                $this->id = $this->db->lastInsertId();
                $this->db->commit();
                return setcookie(
                    self::COOKIE_NAME,
                    $this->token,
                    $this->endTime,
                    '/',
                    settings( 'host' ),
                    settings( 'secure' ),
                    true
                );
            }
        }
        $this->db->rollBack();
        return false;
    }

    /**
     * Read session data from database
     * @return bool
     */
    protected function read()
    {
        $result = false;
        $sql =
            'select
            id,
            useragent,
            ip,
            unix_timestamp(start_time) as startTime,
            unix_timestamp(end_time) as endTime,
            data,
            user
            from usf_sessions
            where token = :token
            and active = 1
            and unix_timestamp(end_time) > :time;';
        if ( $st = $this->db->prepare( $sql ) ) {
            $st->bindValue( ':token', $this->token, Database::PARAM_STR );
            $st->bindValue( ':time', time(), Database::PARAM_INT );
            if ( $st->execute() && $queryResult = $st->fetch( Database::FETCH_ASSOC ) ) {
                $this->id = $queryResult[ 'id' ];
                $this->useragent = $queryResult[ 'useragent' ];
                $this->ip = $queryResult[ 'ip' ];
                $this->startTime = intval( $queryResult[ 'startTime' ] );
                $this->endTime = intval( $queryResult[ 'endTime' ] );
                $this->data = unserialize( $queryResult[ 'data' ] ) or $this->data = [];
                // TODO: get user by id
                $this->user = $queryResult[ 'user' ];
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Saving session data
     * @return bool
     */
    protected function saveData()
    {
        $result = false;
        $sql = 'update usf_sessions 
                set data = :data,
                useragent = :useragent,
                ip = :ip,
                end_time = from_unixtime(:endTime)
                where id = :id';
        if ( $st = $this->db->prepare( $sql ) ) {
            $st->bindValue( ":data", serialize($this->data), Database::PARAM_STR );
            $st->bindValue( ":useragent", $this->useragent, Database::PARAM_STR );
            $st->bindValue( ":ip", $this->ip, Database::PARAM_STR );
            $st->bindValue( ":endTime", $this->endTime, Database::PARAM_INT );
            $st->bindValue( ":id", $this->id, Database::PARAM_INT );
            $result = $st->execute();
        }
        return $result;
    }

    /**
     * Create session token
     * @return string
     */
    protected function createToken()
    {
        do {
            $token = $this->generateToken();
        } while ( $this->checkTokenExists( $token ) );
        return $token;
    }

    /**
     * Token generation method
     * @return string
     */
    protected function generateToken()
    {
        $count = $this->entropy;
        $result = $this->secret;
        do {
            try {
                $result = md5( $result . random_bytes( 32 ) );
            } catch ( \Exception $e ) {
                $result = md5( $result . microtime( true ) . $this->secret );
            }
        } while ( --$count );
        return $result;
    }

    /**
     * Checking if session token already exists
     * @param string $token
     * @return bool
     */
    protected function checkTokenExists( $token )
    {
        $result = false;
        $sql = 'select count(id) id from usf_sessions where token = :token;';
        if ( $st = $this->db->prepare( $sql ) ) {
            $st->bindValue( ':token', $token, Database::PARAM_STR );
            if ( $st->execute() && $queryResult = $st->fetch( Database::FETCH_ASSOC ) ) {
                $result = $queryResult[ 'id' ] > 0;
            }
        }
        return $result;
    }

}