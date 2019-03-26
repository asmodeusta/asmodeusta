<?php

namespace Usf\Admin\Controllers;

use Usf\Core\Base\Controller;

class TestController extends Controller
{

    protected $requestMethod = "get";
    protected $contentType = "html";
    protected $defaults = [
        'lang' => 'en',
        'module' => 'site',
        'controller' => 'main',
        'action' => 'index',
        'page' => '1',
    ];

    public function actionIndex()
    {
        $path = '';
        $routes = [
            [
                "name" => "lang",
                "match" => "([a-z]{2})",
                "value" => "$1",
                "nodes" => [
                    [
                        "name" => "module",
                        "match" => "(site)",
                        "value" => "$1",
                        "nodes" => [
                            [
                                "name" => "controller",
                                "match" => "(main)",
                                "value" => "$1",
                                "nodes" => [
                                    [
                                        "name" => "action",
                                        "match" => "(index)",
                                        "value" => "$1",
                                        "nodes" => [
                                            [
                                                "name" => "page",
                                                "match" => "([1-9][0-9]{0,10})",
                                                "value" => "$1"
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $segments = $this->parseRequestPath( $path, $routes );
        var_dump($segments);
    }

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
            if ( array_key_exists( 'default', $route ) ) {
                $defaultValue = $route[ 'default' ];
            } elseif ( array_key_exists( $route[ 'name' ], $this->defaults ) ) {
                $defaultValue = $this->defaults[ $route[ 'name' ] ];
            } else {
                $defaultValue = null;
            }
            if ( ! $this->matchRouteNew(
                $route,
                $segments,
                $path,
                $defaultValue
            ) ) {
                continue;
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

    private function matchRouteNew( $route, &$segments, &$path, $defaultValue = null )
    {
        $result = true;
        $withNodes = array_key_exists( 'nodes', $route );
        $pattern = '~^' . $route[ 'match' ] . ( $withNodes ? '(.*)' : '' ) .'$~';

        if ( preg_match( $pattern, $path, $matches ) ) {
            $segments[ $route[ 'name' ] ] = preg_replace( $pattern, $route[ 'value' ], $path );
            $path = $withNodes ? end($matches) : '';
        } elseif ( ! is_null( $defaultValue ) && preg_match( $pattern, $defaultValue ) ) {
            $segments[ $route[ 'name' ] ] = preg_replace( $pattern, $route[ 'value' ], $defaultValue );
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

}