<?php

namespace Usf\Core\Components;

use Usf\Core\Base\Component;
use Usf\Core\Base\Exceptions\ControllerException;
use Usf\Core\Base\Exceptions\ModuleException;
use Usf\Core\Base\Exceptions\RequestException;
use Usf\Core\Base\Exceptions\RouterException;
use Usf\Core\Base\Controller;
use Usf\Core\Base\Module;

/**
 * Class Router
 * @package Core\Components
 */
class Router extends Component
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
     * @var Request
     */
    protected $request;

    /**
     * @var Module
     */
    protected $module;

    /**
     * Router constructor.
     * @param array $routes
     */
    public function __construct( array $routes = [] )
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
        $this->addRoutes( $routes );
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
        if ( $overwrite ) {
            $this->routes += $routes;
        } else {
            $this->routes = $routes + $this->routes;
        }

        return $this;
    }

    /**
     * Setting defaults
     *
     * @param array $defaults
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
     * @return Router
     */
    public function parseRequest()
    {
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
                    $callback = ( $this->module = new $moduleClassName() )
                        ->getController( $data[ 'controller' ] )
                        ->getAction( $data[ 'action' ] );
                    $segments[ 'callback' ] = $callback;
                    // Generating request object based on request params
                    $this->request = new Request( $segments );
                } else {
                    throw new RouterException( 'Module "' . $data[ 'module' ] . '" not found!' );
                }
            } catch ( RouterException $exception ) {
                $this->addErrorMessage( $exception->getMessage() );
            } catch ( ModuleException $exception ) {
                $this->addErrorMessage( $exception->getMessage() );
            } catch ( ControllerException $exception ) {
                $this->addErrorMessage( $exception->getMessage() );
            } catch ( RequestException $exception ) {
                $this->addErrorMessage( $exception->getMessage() );
            } catch ( \Exception $exception) {
                $this->addErrorMessage( $exception->getMessage() );
            }
        } else {
            $this->addErrorMessage( 'Wrong request path!' );
        }

        ret:
        return $this;
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
        $oldPath = $path;
        $oldSegments = $segments;

        $needlePos = strpos( $path, '/' );
        if( $needlePos === false ) {
            $needlePos = strlen( $path );
            $section = $path;
        } else {
            $section = substr( $path, 0, $needlePos );
            $needlePos++;
        }

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
                    $route[ 'name' ],
                    $route[ 'match' ],
                    $route[ 'value' ],
                    $segments,
                    $path,
                    $section,
                    $needlePos,
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

    private function matchRoute( $name, $match, $value, &$segments, &$path, $section, $needlePos, $defaultValue = null )
    {
        $result = true;
        $pattern = "~^" . $match . "$~";
        if ( preg_match( $pattern, $section ) ) {
            if ( $name !== 0 ) {
                $segments[ $name ] = preg_replace( $pattern, $value, $section );
            }
            $path = substr( $path, $needlePos );
        } elseif ( ! is_null( $defaultValue ) && preg_match( $pattern, $defaultValue ) ) {
            $segments[ $name ] = preg_replace( $pattern, $value, $defaultValue );
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
                    // noop
                } elseif ( array_key_exists( $route[ 'name' ], $this->defaults ) ) {
                    $defaultValue = $this->defaults[ $route[ 'name' ] ];
                    $section = $segments[ $route[ 'name' ] ];
                    $match = $route[ 'match' ];
                    $value = $route[ 'value' ];
                    $this->remakePattern( $match, $value );

                    $pattern = "~^" . $value . "$~";
                    if ( preg_match( $pattern, $section ) ) {
                        $newValue = preg_replace( $pattern, $route[ 'value' ], $section );
                        if ( $newValue !== $defaultValue ) {
                            $path .= "/" . $newValue;
                        }
                    } else {
                        continue;
                    }
                } else {
                    $section = $segments[ $route[ 'name' ] ];

                    $match = $route[ 'match' ];
                    $value = $route[ 'value' ];
                    $this->remakePattern( $match, $value );

                    $pattern = "~^" . $value . "$~";

                    if ( preg_match( $pattern, $section ) ) {
                        $path .= "/" . preg_replace( $pattern, $match, $section);
                    } else {
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
     * Remakes pattern and value for matching to create url
     *
     * @param $match
     * @param $value
     */
    private function remakePattern( &$match, &$value ) {
        $oldMatch = $match;
        $pattern = "~^([^/(]*)\((.+)\)([^/)]*)$~U";
        $valueArr= [];
        while ( preg_match( $pattern, $oldMatch, $matches ) ) {
            $valueArr[] = $matches[ 2 ];
            $oldMatch = preg_replace( $pattern, "$3", $oldMatch );
        }
        for ( $count = 0; $count < count( $valueArr ); $count++ ) {
            $match = str_replace( "(" . $valueArr[ $count ] . ")", "$" . ( $count + 1 ), $match );
            $value = str_replace( "$" . ( $count + 1 ), "(" . $valueArr[ $count ] . ")", $value );
        }
    }

}