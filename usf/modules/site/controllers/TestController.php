<?php

namespace Usf\Admin\Controllers;

use Usf\Core\Base\Controller;
use Usf\Core\Base\DataStorage;

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

    public function actionTest()
    {
        $iterations = 1000000;

        $arrayStartTime = microtime(true);
        $arr = [];
        for ($i = 0; $i < $iterations; $i++) {
            $arr[$i] = $i;
        }
        $arrayEndTime = microtime(true);

        $objectStartTime = microtime(true);
        $obj = new DataStorage([]);
        for ($i = 0; $i < $iterations; $i++) {
            $obj->$i = $i;
        }
        $objectEndTime = microtime(true);

        echo '<pre>';
        echo 'Arrays time on '.$iterations.' iterations: '.($arrayEndTime - $arrayStartTime).PHP_EOL;
        echo 'Object time on '.$iterations.' iterations: '.($objectEndTime - $objectStartTime).PHP_EOL;
        echo '</pre>';
    }

    public function actionIndex()
    {
        $pathArr = [
            'premmerce/plugins/exchange'
        ];
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
                                    ],
                                    [
                                        "name" => "action",
                                        "match" => "(tester)",
                                        "value" => "$1",
                                    ],
                                    [
                                        "name" => "action",
                                        "match" => "(def)/tone",
                                        "value" => "$1",
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "name" => "module",
                        "match" => "(premmerce)/(plugins)/exchange",
                        "value" => "$1_$2",
                        "nodes" => [
                            [
                                "name" => "controller",
                                "match" => 0,
                                "value" => "main",
                                "nodes" => [
                                    [
                                        "name" => "action",
                                        "match" => 0,
                                        "value" => "index",
                                        "nodes" => [
                                            [
                                                "name" => "param",
                                                "match" => "param([0-9]{1,11})",
                                                "value" => "$1",
                                                "default" => "param1"
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
        foreach ( $pathArr as $path ) {
            var_dump($path);
            $segments = $this->parseRequestPath( $path, $routes );
            var_dump($segments);
            var_dump( $this->generateUrl($segments, $routes ) );
            echo '<hr/>';
        }

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