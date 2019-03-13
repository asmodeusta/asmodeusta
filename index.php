<?php
/*
ini_set('display_errors',1);
error_reporting(E_ALL);

defined('ROOT') or define('ROOT', dirname(__FILE__));
defined('APP') or define('APP', ROOT . '/app');
defined('CORE') or define('CORE', APP . '/core');

require_once CORE . '/components/autoload.php';

$routes = include CORE . '/config/routes_base.php';
if (!empty($_SERVER['REQUEST_URI'])) {
    $uri = trim($_SERVER['REQUEST_URI'], '/');
}

$routes = include APP . '/config/routes.php';

$Router = new Router($routes);
$Router->run();
*/

// +Debug
ini_set('display_errors',1);
error_reporting(E_ALL);
// -Debug

$start_time = microtime(true);

/*
defined('ROOT') or define('ROOT', dirname(__FILE__));
defined('APP') or define('APP', ROOT . '/app');
defined('CORE') or define('CORE', APP . '/core');
defined('MODULES') or define('MODULES', APP . '/modules');

require_once CORE . '/components/autoload.php';

//include 'public/views/index.php';
require_once APP . '/App.php';
//App()->start();

*/

/**
 * New site
 */

defined( 'ROOT' ) or define( 'ROOT', __DIR__ );
defined( 'ASM' ) or define( 'ASM', ROOT . '/asm' );
defined( 'CORE' ) or define( 'CORE', ASM . '/core' );
defined( 'MODULES' ) or define( 'MODULES', ASM . '/modules');

require_once ROOT . '/asm/core/Asm.php';

\Core\Asm();

require_once 'usf/Usf.php';
use Usf\Usf;
$usf = Usf::start();
$usf->init();
$usf->run();
Usf::stop();

//echo '<pre>', microtime(true)-$start_time, '</pre>';