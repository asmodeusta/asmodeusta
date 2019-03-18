<?php

use Usf\Core\Base\Controller;

class MainController extends Controller
{

    public function actionIndex( $page = 1 )
    {
        echo 'Lang: ', usf()->lang, '<br/>Page: ', $page, '<br/>';
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
        var_dump( usf()->router->addRoute( $newRoute ) );
        echo '</pre>';
    }

    public function actionHeart()
    {
        ?>
        <div class="heart"></div>
        <style>
            .heart {
                animation: heartbeat 1s infinite;
                display: inline-block;
                height: 50px;
                width: 50px;

                background-color: red;
                margin: 0 10px;
                position: absolute;
                top: 45%;
                left: 45%;
                transform: rotate(-45deg);
            }
            .heart:before, .heart:after {
                content: "";
                height: 50px;
                width: 50px;
                position: absolute;
                background-color: red;
                border-radius: 50%;
            }
            .heart:before {
                top: -25px;
                left: 0;
            }
            .heart:after {
                left: 25px;
                top: 0;
            }
            @keyframes heartbeat {
                0% {
                    transform: scale(0) rotate(-45deg);
                }
                20% {
                    transform: scale(1.25) translateX(5%) translateY(5%) rotate(-45deg);
                }
                40% {
                    transform: scale(1.5) translateX(9%) translateY(10%) rotate(-45deg);
                }
                100% {
                    transform: scale(1) rotate(-45deg);
                }
            }
        </style>
        <?php
    }

}