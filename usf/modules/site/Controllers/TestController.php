<?php

namespace Usf\Admin\Controllers;

use Usf\Base\Controller;
use Usf\Models\Options;

class TestController extends Controller
{

    public function actionTest()
    {
        $startTime = microtime(true);
        echo '<pre>';
        $options = new Options(0);
        var_dump($options->isset('url'));
        var_dump($options->get('url'));
        var_dump($options->isset('url'));
        $options->set('url', 'asmodeusta.loc');
        var_dump($options->isset('url'));
        var_dump($options->get('url'));
        $options->unset('url');
        var_dump($options->isset('url'));
        var_dump($options->get('url'));
        $options->set('url', 'asmodeusta.loc');
        var_dump($options->get('url'));
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