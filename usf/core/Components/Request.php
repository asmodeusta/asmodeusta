<?php

namespace Usf\Components;

use Usf\Base\Exceptions\RequestException;

/**
 * Class Request
 * @package Usf\Core\Components
 */
class Request
{

    /**
     * Url
     * @var string
     */
    protected $url;

    /**
     * Callback function
     * @var callable
     */
    protected $callback;

    /**
     * Request method
     * @var string
     */
    protected $method;

    /**
     * Content type
     * @var string
     */
    protected $contentType;

    /**
     * Current Module
     * @var string
     */
    protected $module;

    /**
     * Current Controller
     * @var string
     */
    protected $controller;

    /**
     * Current Action
     * @var string
     */
    protected $action;

    /**
     * Current language code
     * @var string
     */
    protected $lang;

    /**
     * Request data
     * @var array
     */
    protected $data = [];

    /**
     * Request constructor.
     * @param array $segments
     * @throws RequestException
     */
    public function __construct(array $segments)
    {
        $this->url = trim($_SERVER[ 'REQUEST_URI' ], '/');

        if (array_key_exists('callback', $segments)) {
            $this->callback = $segments[ 'callback' ];
        } else {
            throw new RequestException('Callback not found!');
        }

        if (array_key_exists('data', $segments)) {
            $this->data = $segments[ 'data' ];
            $this->parseData();
        } else {
            throw new RequestException('Data not found!');
        }
    }

    /**
     * @throws RequestException
     */
    protected function parseData()
    {
        if (is_null($this->module = $this->takeDataValue('module'))) {
            throw new RequestException('Module not found!');
        }

        if (is_null($this->controller = $this->takeDataValue('controller'))) {
            throw new RequestException('Controller not found!');
        }

        if (is_null($this->action = $this->takeDataValue('action'))) {
            throw new RequestException('Action not found!');
        }

        if (is_null($this->lang = $this->takeDataValue('lang'))) {
            throw new RequestException('Lang not found!');
        }

        $this->method = $this->takeDataValue('method') ?? strtolower($_SERVER[ 'REQUEST_METHOD' ]);

        $this->contentType = $this->takeDataValue('type') ?? 'html';
    }

    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name ?? null;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function takeDataValue($key)
    {
        $result = null;
        if (array_key_exists($key, $this->data)) {
            $result = $this->data[ $key ];
            unset($this->data[ $key ]);
        }
        return $result;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getDataValue($key)
    {
        return $this->data[ $key ] ?? null;
    }

    /**
     * Call callback
     */
    public function call()
    {
        call_user_func_array($this->callback, $this->data);
    }

}