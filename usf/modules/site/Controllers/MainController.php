<?php

namespace Usf\Site\Controllers;

use Usf\Base\Controller;
use Usf\Site\Views\MainHeartView;

class MainController extends Controller
{

    public function actionIndex($page = 1)
    {
        echo 'Lang: ', usf()->router()->getRequest()->lang, '<br/>Page: ', $page, '<br/>';
        $time = microtime(true);
        echo 'Time: ', $time - usf()->getStartTime(), '<br/>';
    }

    public function actionUpdate()
    {

    }

    public function actionAdd()
    {
        $newRoute = [
            'name' => 'lang',
            'match' => '(en|ua)',
            'value' => '$1',
            'nodes' => [
                [
                    'name' => 'module',
                    'match' => 'site',
                    'value' => 'site',
                    'nodes' => [
                        [
                            'name' => 'controller',
                            'match' => 'settings',
                            'value' => 'settings',
                            'nodes' => [
                                [
                                    'name' => 'action',
                                    'match' => 'float',
                                    'value' => 'float',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        echo '<pre>';
        if (router()->addRoute($newRoute)) {
            echo 'success!';
        } else {
            echo 'failed!';
        }
        echo '</pre>';
    }

    public function actionHeart()
    {
        new MainHeartView($this->module);
    }

}