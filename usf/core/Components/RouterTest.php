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

    public function __construct($requestPath, $requestQuery = '', $requestMethod = 'get')
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
        if (empty($this->routes) && array_key_exists('routes', $this->configuration)) {
            $this->routes = $this->configuration[ 'routes' ];
        }

        /**
         * Setup defaults
         */
        if (empty($this->defaults) && array_key_exists('defaults', $this->configuration)) {
            $this->defaults = $this->configuration[ 'defaults' ];
        }
    }

    public function parseRequest()
    {
        $path = $this->requestPath;

        $data = $this->parseRequestPath($path, $this->routes);

        return $data;
    }

    protected function parseRequestPath(&$path, $routes)
    {
        $path = trim($path, '/');
        $oldPath = $path;
        $segments = [];

        foreach ($routes as $route) {

            $args = [];

            if (!$this->validateRoute($route)) {
                continue;
            }

            // Check method
            if (array_key_exists('method', $route)) {
                if (strpos($route[ 'method' ], $this->requestMethod) === false) {
                    continue;
                }
            }

            // TODO: Check content type

            // Check match
            if ($route[ 'match' ] === 0) {
                $args[ $route[ 'name' ] ] = $route[ 'value' ];
            } else {
                if (array_key_exists('default', $route)) {
                    $defaultValue = $route[ 'default' ];
                } elseif (array_key_exists($route[ 'name' ], $this->defaults)) {
                    $defaultValue = $this->defaults[ $route[ 'name' ] ];
                } else {
                    $defaultValue = null;
                }
                if (!$this->matchRoute(
                    $route,
                    $args,
                    $path,
                    $defaultValue
                )) {
                    continue;
                }
            }

            // Check nodes
            if (array_key_exists('nodes', $route)) {
                $childArgs = $this->parseRequestPath($path, $route[ 'nodes' ]);
                foreach ($childArgs as $arg) {
                    if (array_key_exists('success', $arg) && $arg[ 'success' ]) {
                        $segments[] = array_merge($args, $arg);
                    }
                }
            } else {
                // Check if all sections was processed
                if ($path === "") {
                    $segments[] = array_merge($args, ['success' => true]);
                }
            }
            $path = $oldPath;
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
    private function matchRoute($route, &$segments, &$path, $defaultValue = null)
    {
        $result = true;
        $checkedPath = $path . '/';

        $withNodes = array_key_exists('nodes', $route);
        $pattern = '~^' . $route[ 'match' ] . '/' . ($withNodes ? '(.*)' : '') . '$~';

        if (preg_match($pattern, $checkedPath, $matches)) {
            $segments[ $route[ 'name' ] ] = preg_replace($pattern, $route[ 'value' ], $checkedPath);
            $path = $withNodes ? end($matches) : '';
        } elseif (!is_null($defaultValue) && preg_match($pattern, $defaultValue . '/')) {
            $segments[ $route[ 'name' ] ] = preg_replace($pattern, $route[ 'value' ], $defaultValue . '/');
        } else {
            $result = false;
        }
        return $result;
    }

    private function validateRoute(array $route)
    {
        return array_key_exists('name', $route)
            && array_key_exists('match', $route)
            && array_key_exists('value', $route);
    }

}