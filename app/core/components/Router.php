<?php

class Router
{

    const DEFAULT_ROUTES_FILE = APP . "/config/routes.json";

    private $routes = [];

    public function __construct($filename = null)
    {
        if(is_file($filename)&&substr($filename, strlen($filename-5),5)===".json") {

        } else {
            $filename = self::DEFAULT_ROUTES_FILE;
        }
        $json_string = file_get_contents($filename);
        $this->routes = json_decode($json_string);
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function run($url, &$moduleObj) {
        $result = false;

        $url = trim($url, '/');
        $url = strpos($url, '?')==0?$url:substr($url, 0, strpos($url, '?'));
        $urlParams = $this->parseUrl($url, $this->routes);
        if (isset($urlParams['module'])&&isset($urlParams['controller'])&&isset($urlParams['action'])) {
            $module = $urlParams['module'];
            $controller = $urlParams['controller'];
            $action = $urlParams['action'];

            unset($urlParams['module']);unset($urlParams['controller']);unset($urlParams['action']);

            $moduleName = ucfirst($module) . 'Module';
            $moduleDir = MODULES . '/' . $module;

            if(is_dir($moduleDir)) {
                $moduleFile = $moduleDir . '/' . $moduleName . '.php';
                if(is_file($moduleFile)) {
                    include_once $moduleFile;

                    $moduleObj = new $moduleName($controller, $action);
                    $result = $moduleObj->run($urlParams);
                }
            }
        }
        return $result;
    }

    private function parseUrl(&$url, $routes, &$result = []) {
        $url = trim($url, '/');
        $needlePos = strpos($url, '/');
        if($needlePos==0) {
            $needlePos = strlen($url);
            $section = $url;
        } else {
            $section = substr($url, 0, $needlePos);
        }

        foreach ($routes as $route) {
            if(isset($route->method)) {
                if(strpos($route->method,$this->method)==0) {
                    continue;
                } else {
                    $result["method"] = $this->method;
                }
            }
            $pattern = "~^" . $route->match . "$~";
            //echo "$route->name ($pattern => $route->value): section '$section'<br/>";
            if (preg_match($pattern, $section)) {
                $result[$route->name] = preg_replace($pattern, $route->value, $section);
                $url = substr($url, $needlePos);
                if (isset($route->nodes)) {
                    $result = array_merge($result, $this->parseUrl($url, $route->nodes, $result));
                }
            } elseif ($route->match === 0) {
                if (isset($route->nodes)) {
                    $result[$route->name] = $route->value;
                    $newRes = $this->parseUrl($url, $route->nodes, $result);
                    $result = array_merge($result, $newRes);
                } elseif($section==="") {
                    $result[$route->name] = $route->value;
                }
            } else {
                continue;
            }
            //var_dump($result);
            //echo " ___________ url: $url<br/>";
            if($url===""&&isset($result['module'])&&isset($result['controller'])&&isset($result['action'])) {
                break;
            }
        }
        return $result;
    }

    public function createUrl($params) {
        return $this->_createUrl($params, $this->routes);
    }

    protected function _createUrl($params, $routes) {
        $url = "";
        foreach ($routes as $route) {
            if(isset($route->method)&&isset($params["method"])&&strpos($route->method,$params["method"])==0) {
                continue;
            }
            if(isset($route->name)&&isset($route->value)&&isset($route->match)&&isset($params[$route->name])) {
                $section = $params[$route->name];

                if(isset($route->_match)&&isset($route->_value)) {
                    $value = $route->_match;
                    $match = $route->_value;
                } else {
                    $match = $route->match;
                    $value = $route->value;
                    $this->remakePattern($match, $value);
                }

                $pattern = "~^" . $value . "$~";
                //echo "$route->name ($pattern => $match): section '$section'<br/>";
                if ($route->match === 0) {
                    if(isset($route->nodes)) {
                        $url .= $this->_createUrl($params, $route->nodes);
                    }
                } elseif (preg_match($pattern, $section)) {
                    $url .= "/" . preg_replace($pattern, $match, $section);
                    unset($params[$route->name]);
                    if(isset($route->nodes)) {
                        $url .= $this->_createUrl($params, $route->nodes);
                    }
                } else
                if(count($params)===0) {
                    break;
                }
            }
        }
        return $url;
    }

    protected function remakePattern(&$match, &$value) {
        $temp_match = $match;
        $pattern1 = "~^(.*)\((.+)\)(.*)$~U";
        $valueArr= [];
        while (preg_match($pattern1, $temp_match, $matches)) {
            $valueArr[] = $matches[2];
            $temp_match = preg_replace($pattern1, "$3", $temp_match);
        }
        for($count = 0;$count<count($valueArr);$count++) {
            $match = str_replace("(" . $valueArr[$count] . ")", "$".($count+1), $match);
            $value = str_replace("$".($count+1), "(".$valueArr[$count].")", $value);
        }
    }


}