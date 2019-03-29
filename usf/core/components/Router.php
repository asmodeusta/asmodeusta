<?php

namespace Usf\Core\Components;

use Usf\Core\Base\Component;
use Usf\Core\Base\ConfigHandler;
use Usf\Core\Base\Exceptions\RouterException;
use Usf\Core\Base\Factories\ConfigHandlerFactory;
use Usf\Core\Base\Interfaces\ConfigurableInterface;
use Usf\Core\Base\Module;


/**
 * Class Router
 * @package Core\Components
 */
class Router extends Component implements ConfigurableInterface
{
    /**
     * Request URL
     * @var string
     */
    private $requestPath;

    /**
     * Request query
     * @var string
     */
    private $requestQuery;

    /**
     * Request Method
     * @var string
     */
    private $requestMethod;

    /**
     * Content Type
     * @var string
     */
    private $contentType;

    /**
     * Default route names
     * @var array
     */
    private $defaults = [];

    /**
     * Routes
     * @Keys:
     * - @name (required): name of route segment (module, controller, action or other param name)
     * - @match (required): pattern for comparison or 0 for empty segment
     * - @value (required): the string to replace after comparison match
     * - @nodes (optional): child nodes of the route.
     *      If not set then Router compares all parts of route, and if it's OK - stops searching
     * - @method (optional): the string of available methods for this and child routes.
     *      Available values: get|post
     *      By default route can be called with any method or method of parent route.
     * - @type (optional): available content type to return for this and child routes.
     *      Available values: html|xml|json|ajax
     *      By default route can be used to return any type of content, or it can be declared later in Controller
     *
     * @var array
     */
    private $routes = [];

    /**
     * Routes config handler
     * @var ConfigHandler
     */
    protected $configHandler;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Module
     */
    protected $module;

    /**
     * Router constructor.
     * @param string $configFile
     */
    public function __construct( $configFile )
    {
        /**
         * Setting request data
         */
        $requestData = parse_url( $_SERVER[ 'REQUEST_URI' ] );
        $this->requestQuery = array_key_exists( 'query', $requestData ) ? '?' . $requestData[ 'query' ] : '';
        $this->requestPath = array_key_exists( 'path', $requestData ) ? trim( $requestData[ 'path' ], '/' ) : '';

        /**
         * Setting request method
         */
        $this->requestMethod = strtolower( $_SERVER[ 'REQUEST_METHOD' ] );

        /**
         * Setting content type
         */
        $this->contentType = 'html';

        /**
         * Adding routes
         */
        $this->setupConfigFromFile( $configFile );
    }

    /**
     * @param string $file
     */
    public function setupConfigFromFile( $file )
    {
        $this->configHandler = ConfigHandlerFactory::create( $file );
        $this->setupConfig( $this->configHandler->getFullConfig() );
    }

    /**
     * @param array $config
     */
    public function setupConfig( array $config )
    {
        /**
         * Setup routes
         */
        if ( empty( $this->routes ) && array_key_exists( 'routes', $config ) ) {
            $this->routes = $config[ 'routes' ];
        }

        /**
         * Setup defaults
         */
        if ( empty( $this->defaults ) && array_key_exists( 'defaults', $config ) ) {
            $this->defaults = $config[ 'defaults' ];
        }
    }

    /**
     * Adding route
     *
     * @param $route
     * @param bool $rewrite
     * @return bool
     */
    public function addRoute( $route, $rewrite = false )
    {
        $result = false;
        if ( $this->validateRoute( $route ) ) {
            $routes = [ $route ];
            $routes = $this->addLangToRoutes( $routes );
            $result =  $this->addRouteRecursive( $this->routes, $routes, $rewrite );
        }
        return $result;
    }

    /**
     * Add lang to routes
     * @param array $routes
     * @return array
     */
    protected function addLangToRoutes( array &$routes )
    {
        $routes = [
            "name" => "lang",
            "match" => "([a-z]{2})",
            "value" => "$1",
            "nodes" => $routes
        ];
        return $routes;
    }

    /**
     * Recursive search and adding route
     *
     * @param array $routes
     * @param array $newRoute
     * @param bool $rewrite
     * @return bool
     */
    protected function addRouteRecursive( &$routes, $newRoute, $rewrite = false )
    {
        $result = false;
        if ( $this->validateRoute( $newRoute ) ) {
            //Find route
            $found = false;
            $oldRoute = [];
            foreach ( $routes as &$oldRoute ) {
                if ( $this->validateRoute( $oldRoute ) && empty( $this->compareRoutes( $oldRoute, $newRoute ) ) ) {
                    $found = true;
                    break;
                }
            }

            // If route has been found, compare
            if ( $found ) {
                if ( array_key_exists( 'nodes', $newRoute ) ) {
                    if ( array_key_exists( 'nodes', $oldRoute ) ) {
                        $result = true;
                        foreach ( $newRoute[ 'nodes' ] as $node ) {
                            $result = $result && $this->addRouteRecursive( $oldRoute[ 'nodes' ], $node, $rewrite );
                        }
                    } else {
                        $routes[ 'nodes' ] = $newRoute[ 'nodes' ];
                        $result = true;
                    }
                }
            } else {
                // If not found - add new route into current routes
                $routes[] = $newRoute;
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @param array $oldRoute
     * @param array $newRoute
     * @return array
     */
    protected function compareRoutes( $oldRoute, $newRoute )
    {
        return array_diff(
            array_filter( $oldRoute, function ( $var ) { return ! is_array( $var ); } ),
            array_filter( $newRoute, function ( $var ) { return ! is_array( $var ); } )
        );
    }

    /**
     * Adding routes
     *
     * @param array $routes
     * @param bool $overwrite
     * @return Router
     */
    public function addRoutes( array $routes, $overwrite = false )
    {
        foreach ( $this->addLangToRoutes( $routes ) as $route ) {
            $this->addRoute( $route, $overwrite );
        }
        return $this;
    }

    /**
     * Adding routes from config file
     *
     * @param $file
     * @return $this
     */
    public function addRoutesFromFile( $file )
    {
        $configHandler = ConfigHandlerFactory::create( $file );
        $routes = $configHandler->setSection( 'routes' )->getConfig();
        $this->addRoutes( $routes );
        return $this;
    }

    /**
     * Setting defaults
     *
     * @param array $defaults
     * @param bool $overwrite
     * @return Router
     */
    public function addDefaults( array $defaults, $overwrite = false )
    {
        if ( $overwrite ) {
            $this->defaults = $defaults;
        } else {
            $this->defaults = $defaults + $this->defaults;
        }

        return $this;
    }

    /**
     * Main method of the Router
     *
     * @return bool
     */
    public function parseRequest()
    {
        $result = false;

        $path = $this->requestPath;
        // Parsing request path to get module, controller, action and other request params
        $data = $this->parseRequestPath( $path, $this->routes );
        // Checking request data
        if ( $this->checkRequestData( $data ) ) {
            // Checking if url is valid
            if ( $this->requestMethod === 'get' ) {
                // Creating url based on parsed request params
                $path = $this->createUrl( $data );
                // Check if generated path is different from actual, then redirect
                if ( $path !== $this->requestPath ) {
                    $url = '/' . $path . ( empty( $this->requestQuery ) ? '' : '/' . $this->requestQuery );
                    redirect( $url );
                }
            }
            $segments[ 'data' ] = $data;
            // Searching callback for current request
            try {
                $moduleClassName = ucfirst( $data[ 'module' ] ) . 'Module';
                $moduleFile = DIR_MODULES . '/' . $data[ 'module' ] . '/' . $moduleClassName . '.php';
                if ( is_file( $moduleFile ) ) {
                    include_once $moduleFile;
                    $moduleClass = lastDeclaredClass();
                    $segments['callback'] = ( new $moduleClass( $data['controller'], $data['action'] ) )->getCallback();
                    // Generating request object based on request params
                    $this->request = new Request( $segments );
                    $result = true;
                } else {
                    throw new RouterException( 'Module "' . $data[ 'module' ] . '" not found!' );
                }
            } catch ( \Exception $exception) {
                $this->addErrorMessage( $exception->getMessage() );
            }
        } else {
            $this->addErrorMessage( 'Wrong request path!' );
        }

        ret:
        return $result;
    }

    /**
     * Check request segments
     * @param array $segments
     * @return bool
     */
    private function checkRequestData(array $segments )
    {
        return array_key_exists( 'module', $segments )
            && array_key_exists( 'controller', $segments )
            && array_key_exists( 'action', $segments );
    }

    /**
     * Get request segments
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Parsing request path
     *
     * @param string $path
     * @param array $routes
     * @param array $segments
     * @return array
     */
    private function parseRequestPath( &$path, $routes, &$segments = [] )
    {
        $path = trim( $path, '/' );
        $oldPath = $path;
        $oldSegments = $segments;

        foreach ( $routes as $route ) {

            if ( ! $this->validateRoute( $route ) ) {
                continue;
            }

            // Check method
            if ( array_key_exists( 'method', $route ) ) {
                if ( strpos( $route[ 'method' ], $this->requestMethod ) === false ) {
                    continue;
                } else {
                    $segments[ 'method' ] = $this->requestMethod;
                }
            }

            // Check content type
            if ( array_key_exists( 'type', $route ) ) {
                if ( strpos( $route[ 'type' ], $this->contentType ) === false ) {
                    continue;
                } else {
                    $segments[ 'type' ] = $this->contentType;
                }
            }

            // Check match
            if ( $route[ 'match' ] === 0 ) {
                $segments[ $route[ 'name' ] ] = $route[ 'value' ];
            } else {
                if ( array_key_exists( 'default', $route ) ) {
                    $defaultValue = $route[ 'default' ];
                } elseif ( array_key_exists( $route[ 'name' ], $this->defaults ) ) {
                    $defaultValue = $this->defaults[ $route[ 'name' ] ];
                } else {
                    $defaultValue = null;
                }
                if ( ! $this->matchRoute(
                    $route,
                    $segments,
                    $path,
                    $defaultValue
                ) ) {
                    continue;
                }
            }

            // Check nodes
            if ( array_key_exists( 'nodes', $route ) ) {
                $this->parseRequestPath($path, $route[ 'nodes' ], $segments );
            }

            // Check if all sections was processed
            if ( $path === "" ) {
                break;
            } else {
                $path = $oldPath;
                $segments = $oldSegments;
            }

        }

        //
        if ( $path !== "" ) {
            $segments = [];
        }

        return $segments;
    }

    /**
     * Matching routes with request path
     *
     * @param $route
     * @param $segments
     * @param $path
     * @param null $defaultValue
     * @return bool
     */
    private function matchRoute( $route, &$segments, &$path, $defaultValue = null )
    {
        $result = true;
        $checkedPath = $path . '/';

        $withNodes = array_key_exists( 'nodes', $route );
        $pattern = '~^' . $route[ 'match' ] . '/' . ( $withNodes ? '(.*)' : '' ) .'$~';

        if ( preg_match( $pattern, $checkedPath, $matches ) ) {
            $segments[ $route[ 'name' ] ] = preg_replace( $pattern, $route[ 'value' ], $checkedPath );
            $path = $withNodes ? end($matches) : '';
        } elseif ( ! is_null( $defaultValue ) && preg_match( $pattern, $defaultValue . '/' ) ) {
            $segments[ $route[ 'name' ] ] = preg_replace( $pattern, $route[ 'value' ], $defaultValue . '/' );
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Validating route
     *
     * @param array $route
     * @return bool
     */
    private function validateRoute( array $route )
    {
        return array_key_exists( 'name', $route )
            && array_key_exists( 'match', $route )
            && array_key_exists( 'value', $route );
    }

    /**
     * Create Url
     *
     * @param array $segments
     * @return string
     */
    public function createUrl( array $segments )
    {
        return $this->generateUrl( $segments, $this->routes );
    }

    /**
     * Create url based on routes
     *
     * @param array $segments
     * @param array $routes
     * @return string
     */
    public function generateUrl( array &$segments, array $routes )
    {
        $path = "";

        foreach ( $routes as $route ) {

            if ( ! $this->validateRoute( $route ) ) {
                continue;
            }

            // Check method
            if ( array_key_exists( 'method', $route ) && array_key_exists( 'method', $segments ) ) {
                if ( strpos( $route[ 'method' ], $segments[ 'method' ] ) === false ) {
                    continue;
                }
            }

            // Check content type
            if ( array_key_exists( 'type', $route ) && array_key_exists( 'type', $segments ) ) {
                if ( strpos( $route[ 'type' ], $segments[ 'type' ] ) === false ) {
                    continue;
                }
            }

            // Check match
            if ( array_key_exists( $route[ 'name' ], $segments ) ) {

                if ( $route[ 'match' ] === 0 ) {
                    //
                } else {
                    if ( array_key_exists( 'default', $route ) ) {
                        $defaultValue = $route[ 'default' ];
                    } elseif ( array_key_exists( $route[ 'name' ], $this->defaults ) ) {
                        $defaultValue = $this->defaults[ $route[ 'name' ] ];
                    } else {
                        $defaultValue = null;
                    }
                    if ( ! $this->matchRouteForCreate(
                        $route[ 'name' ],
                        $route[ 'match' ],
                        $route[ 'value' ],
                        $segments,
                        $path,
                        $defaultValue
                    ) ) {
                        continue;
                    }
                }

                if ( array_key_exists( 'nodes', $route ) ) {
                    $path .= '/' . $this->generateUrl( $segments, $route[ 'nodes' ] );
                }
            }
        }

        return trim( $path, '/' );
    }

    /**
     * Matching routes to create url
     *
     * @param string $name
     * @param string $match
     * @param string $value
     * @param array $segments
     * @param string $path
     * @param null|string $defaultValue
     * @return bool
     */
    private function matchRouteForCreate( $name, $match, $value, $segments, &$path, $defaultValue = null )
    {
        $result = true;
        $section = $segments[ $name ];
        $this->remakePattern( $match, $value );

        $pattern = "~^" . $value . "$~";
        if ( preg_match( $pattern, $section ) ) {
            $newValue = preg_replace( $pattern, $match, $section);
            if ( $newValue !== $defaultValue ) {
                $path .= "/" . $newValue;
            }
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Remakes pattern and value for matching to create url
     *
     * @param string $match
     * @param string $value
     */
    private function remakePattern( &$match, &$value ) {
        $oldMatch = $match;
        $pattern = "~([\(](.*)[\)])~U";
        $valueArr= [];
        while ( preg_match( $pattern, $oldMatch, $matches ) ) {
            $valueArr[] = $matches[ 2 ];
            $oldMatch = preg_replace( $pattern, "$2", $oldMatch, 1 );
        }
        for ( $count = 0; $count < count( $valueArr ); $count++ ) {
            $match = str_replace( "(" . $valueArr[ $count ] . ")", "$" . ( $count + 1 ), $match );
            $value = str_replace( "$" . ( $count + 1 ), "(" . $valueArr[ $count ] . ")", $value );
        }
    }

}