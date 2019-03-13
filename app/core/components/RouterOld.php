<?php

class RouterOld
{

    const RULE_PATTERN = '~<([a-z]{1,20})\:(.*)>~U';
    const PARAM_RULE_PATTERN = '/<param:.*>';
    const PARAM_RULE_PATH = '/<param>';

    const DEFAULT_MODULE = 'site';
    const DEFAULT_CONTROLLER = 'site';
    const DEFAULT_ACTION = 'index';

    const DEFAULT_ROUTES_PATH = CORE . '/config/routes_base.php';

    private $uri;
    private $routes;

    public function __construct($routes = []) {
        $routesPath = self::DEFAULT_ROUTES_PATH;
        $defaultRoutes = is_file($routesPath)?include $routesPath:[];
        $defaultRoutesParams = [];
        foreach ($defaultRoutes as $pattern => $path) {
            $defaultRoutesParams[trim($pattern . self::PARAM_RULE_PATTERN, '/')] = trim($path . self::PARAM_RULE_PATH, '/');
        }
        $this->routes = array_merge($routes, $defaultRoutes, $defaultRoutesParams);
    }

    private function getURI() {
        if(!isset($this->uri)) {
            if (!empty($_SERVER['REQUEST_URI'])) {
                $uri = trim($_SERVER['REQUEST_URI'], '/');
                $this->uri = strpos($uri, '?')==0?$uri:substr($uri, 0, strpos($uri, '?'));
                var_dump($this->uri);
            } else {
                // error
                die('Error!');
            }
        }
        return $this->uri;
    }

    private function demuxRoute(&$pattern, &$path) {
        $count = 0;
        while(preg_match(self::RULE_PATTERN, $pattern, $matches)) {
            $path = str_replace('<' . $matches[1] . '>', '$' . ++$count, $path);
            $pattern = preg_replace(self::RULE_PATTERN, '($2)', $pattern, 1);
        }
    }

    private function getRoute(&$pattern, &$path) {
        $uri = $this->getURI();
        $this->demuxRoute($pattern, $path);
        if (preg_match('~^' . $pattern . '$~', $uri)) {
            return preg_replace('~^' . $pattern . '$~', $path, $uri);
        }
        return false;
    }

    public function run() {
        echo '<pre>';
        foreach ($this->routes as $pattern => $path) {
            $currentRoute = $this->getRoute($pattern, $path);
            echo $pattern, ' => ', $path, '  |  ';
            var_dump($currentRoute);
        }
        echo '</pre>';
    }

}