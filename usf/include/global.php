<?php

namespace Usf\u7fb342acc05748423d8d73d49fdf1f4e {

    use Usf\Core\Components\Database;
    use Usf\Core\Components\Request;
    use Usf\Core\Components\Router;
    use Usf\Core\Components\Settings;
    use Usf\Usf;

    final class UsfGlobals
    {

        private static $instance = null;

        private $usf = null;
        private $db = null;
        private $settings = null;
        private $router = null;
        private $request = null;

        public static function go()
        {
            return self::$instance ?? self::$instance = new self();
        }

        private function __construct()
        {

        }

        private function __clone()
        {
            // TODO: Implement __clone() method.
        }

        private function __sleep()
        {
            // TODO: Implement __sleep() method.
        }

        private function __wakeup()
        {
            // TODO: Implement __wakeup() method.
        }

        /**
         * @return Usf
         */
        public function getUsf()
        {
            return $this->usf;
        }

        /**
         * @param Usf $usf
         */
        public function setUsf($usf): void
        {
            $this->usf = $this->usf ?? $usf;
        }

        /**
         * @return Database
         */
        public function getDb()
        {
            return $this->db;
        }

        /**
         * @param Database $db
         */
        public function setDb($db): void
        {
            $this->db = $this->db ?? $db;
        }

        /**
         * @return Settings
         */
        public function getSettings()
        {
            return $this->settings;
        }

        /**
         * @param Settings $settings
         */
        public function setSettings($settings): void
        {
            $this->settings = $this->settings ?? $settings;
        }

        /**
         * @return Router
         */
        public function getRouter()
        {
            return $this->router;
        }

        /**
         * @param Router $router
         */
        public function setRouter($router): void
        {
            $this->router = $this->router ?? $router;
        }

        /**
         * @return Request
         */
        public function getRequest()
        {
            return $this->request;
        }

        /**
         * @param Request $request
         */
        public function setRequest($request): void
        {
            $this->request = $this->request ?? $request;
        }

    }

}

/**
 * Get Usf single object
 * @return Usf\Usf
 */
function usf()
{
    global $_USF;
    return $_USF;
}

/**
 * Get Database object
 * @return Usf\Core\Components\Database
 */
function db()
{
    global $_USF_DB;
    return $_USF_DB;
}

/**
 * Get Router object
 * @return Usf\Core\Components\Router
 */
function router()
{
    global $_USF_ROUTER;
    return $_USF_ROUTER;
}

/**
 * Get Request object
 * @return Usf\Core\Components\Request
 */
function request()
{
    global $_USF_REQUEST;
    return $_USF_REQUEST;
}

/**
 * Get Settings
 * @param string $name
 * @return Usf\Core\Components\Settings|mixed
 */
function settings( $name = '' )
{
    global $_USF_SETTINGS;
    if ( $name === '' ) {
        return $_USF_SETTINGS;
    } else {
        return $_USF_SETTINGS->$name;
    }
}