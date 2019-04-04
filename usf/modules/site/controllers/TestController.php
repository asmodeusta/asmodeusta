<?php

namespace Usf\Admin\Controllers;

use Usf\Core\Base\Controller;
use Usf\Core\Base\DataStorage;
use function Usf\__usf;

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
        $iterations = 1000000;

        $globalStartTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $r = usf();
        }
        $globalEndTime = microtime(true);

        echo '<pre>';
        echo 'Global time on '.$iterations.' iterations: '.($globalEndTime - $globalStartTime).PHP_EOL;
        echo '</pre>';
    }

}