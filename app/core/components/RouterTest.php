<?php

class RouterTest
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

    public function run($url) {
        $url = trim($url, '/');
        $url = strpos($url, '?')==0?$url:substr($url, 0, strpos($url, '?'));
        echo '<pre>';
        $urlParams = $this->parseUrl($url, $this->routes);
        if($url==="") {

        } else {
            var_dump($url);
        }
        var_dump($urlParams);
        echo "<br/>/////////////////////////////////////////////////////////////////////////////////////<br/><br/>";
        var_dump($this->createUrl($urlParams));
        echo '</pre>';
        if (isset($urlParams['module'])&&isset($urlParams['controller'])&&isset($urlParams['action'])) {

        } else {
            echo "error!";
        }
    }

    private function parseUrl(&$url, $routes) {
        $url = trim($url, '/');
        $result = [];
        $needlePos = strpos($url, '/');
        if($needlePos==0) {
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
            if (preg_match($pattern, $section)) {
                $result[$route->name] = preg_replace($pattern, $route->value, $section);
                $url = substr($url, $needlePos);
                if (isset($route->nodes)) {
                    $result = array_merge($result, $this->parseUrl($url, $route->nodes));
                }
            } elseif ($route->match === 0) {
                $result[$route->name] = $route->value;
                if (isset($route->nodes)) {
                    $newRes = $this->parseUrl($url, $route->nodes);
                    $result = array_merge($result, $newRes);
                }
            }
            if($url==="") {
                break;
            } else {
                var_dump($url);
            }
        }
        return $result;
    }

    public function createUrl($params) {
        $url = "";
        $this->_createUrl($params, $this->routes, $url);
        return $url;
    }

    protected function _createUrl($params, $routes, &$url) {
        $result = false;
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
                if ($route->match === 0) {
                    if(isset($route->nodes)) {
                        $result = $this->_createUrl($params, $route->nodes, $url);
                    }
                } elseif (preg_match($pattern, $section)) {
                    $url .= "/" . preg_replace($pattern, $match, $section);
                    if(isset($route->nodes)) {
                        $result = $this->_createUrl($params, $route->nodes, $url);
                    }
                } else {
                    continue;
                }
                if($result) {
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