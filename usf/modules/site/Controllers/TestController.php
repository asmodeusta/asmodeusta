<?php

namespace Usf\Admin\Controllers;

use Usf\Base\Controller;
use Usf\Base\Exceptions\FileManagerException;
use Usf\Components\FileManager;
use Usf\Models\Options;

class TestController extends Controller
{

    public function actionTest()
    {
        $startTime = microtime(true);
        echo '<pre>';
        try {
            $fm1 = new FileManager();
            $fm2 = new FileManager($this->getDirectory());
            $fm3 = new FileManager(DIR_ROOT);

            var_dump($dirs = $fm1->dir());
            foreach ($fm1->mapDir() as $path) {
                var_dump($path);
            }
        } catch (FileManagerException $exception) {
            print_r($exception->getMessage());
        }

        echo microtime(true) - $startTime, '<br/>';
        echo microtime(true) - usf()->getStartTime(), '<br/>';
        echo '</pre>';
    }

    public function actionIndex()
    {
        $iterations = 1000000;

        $usfStartTime = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            usf();
        }
        $usfEndTime = microtime(true);

        echo '<pre>';
        echo '/usf() time on ' . $iterations . ' iterations: ' . ($usfEndTime - $usfStartTime) . PHP_EOL;
        echo '</pre>';
    }

}