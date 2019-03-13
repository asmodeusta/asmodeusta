<?php

namespace Usf\Core\Components;

use Usf\Core\Base\Exceptions\RequestException;

/**
 * Class Request
 * @package Usf\Core\Components
 */
class Request
{

    protected $url;

    protected $callback;

    protected $module;

    protected $controller;

    protected $action;

    protected $data = [];

    /**
     * Request constructor.
     * @param array $segments
     * @throws RequestException
     */
    public function __construct( array $segments )
    {
        $this->url = trim( $_SERVER[ 'REQUEST_URI' ], '/' );

        if ( array_key_exists( 'callback', $segments ) ) {
            $this->callback = $segments[ 'callback' ];
        } else {
            throw new RequestException( 'Callback not found!' );
        }

        if ( array_key_exists( 'data', $segments ) ) {
            $this->data = $segments[ 'data' ];
            $this->parseData();
        } else {
            throw new RequestException( 'Data not found!' );
        }
    }

    /**
     * @throws RequestException
     */
    protected function parseData()
    {
        if ( is_null( $this->module = $this->takeDataValue( 'module' ) ) ) {
            throw new RequestException( 'Module not found!' );
        }

        if ( is_null( $this->controller = $this->takeDataValue( 'controller' ) ) ) {
            throw new RequestException( 'Controller not found!' );
        }

        if ( is_null( $this->action = $this->takeDataValue( 'action' ) ) ) {
            throw new RequestException( 'Action not found!' );
        }
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function takeDataValue( $key )
    {
        $result = null;
        if ( array_key_exists( $key, $this->data ) ) {
            $result = $this->data[ $key ];
            unset( $this->data[ $key ] );
        }
        return $result;
    }

    public function call()
    {
        call_user_func_array( $this->callback, $this->data );
    }

}