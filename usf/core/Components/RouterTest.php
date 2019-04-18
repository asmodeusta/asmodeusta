<?php


namespace Usf\Components;


use Usf\Base\Traits\Configurable;
use Usf\Base\Traits\Observable;

class RouterTest
{

    use Configurable;

    use Observable;

    protected $requestPath;

    protected $requestQuery;

    protected $requestMethod;

    protected $routes = [];

    protected $defaults = [];

    public function __construct( $requestPath, $requestQuery = '', $requestMethod = 'get' )
    {
        $this->requestPath = $requestPath;
        $this->requestQuery = $requestQuery;
        $this->requestMethod = $requestMethod;
    }

    protected function setup()
    {
        /**
         * Setup routes
         */
        if ( empty( $this->routes ) && array_key_exists( 'routes', $this->configuration ) ) {
            $this->routes = $this->configuration[ 'routes' ];
        }

        /**
         * Setup defaults
         */
        if ( empty( $this->defaults ) && array_key_exists( 'defaults', $this->configuration ) ) {
            $this->defaults = $this->configuration[ 'defaults' ];
        }
    }

}