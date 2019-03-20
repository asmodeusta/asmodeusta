<?php

namespace Usf\Site\Controllers;

use Usf\Core\Base\Controller;
use Usf\Core\Base\Factories\ConfigHandlerFactory;

class SettingsController extends Controller
{
    public function actionIndex()
    {
        $usf = usf();
        $settings = $usf->getSettings();
        echo '<pre>';
        if ( $theme = $settings->getTheme() ) {
            $themeDir = DIR_USF . DS . 'themes' . DS . $theme;
            if ( is_dir( $themeDir ) ) {
                $fileAbout = $themeDir . DS . 'about.php';
                if ( is_file( $fileAbout ) ) {
                    $about = include $fileAbout;
                    var_dump( $about );
                }
            }
        }
        var_dump( base64_encode( random_bytes ( 32 ) ) );
        echo microtime( true ) - usf()->getStartTime();

        echo '<pre>';
    }

    public function actionGuid()
    {
        echo uniqid( '', true );
    }

    public function actionCheck()
    {
        $filePhp = DIR_USF . DS . 'config' . DS . 'cf.php';
        $fileJson = DIR_USF . DS . 'config' . DS . 'cf.json';
        $fileIni = DIR_USF . DS . 'config' . DS . 'cf.ini';
        $data = [
            "routes" => [
                'module' => [
                    'match' => 'site',
                    'value' => 'site',
                    'nodes' => [
                        'controller' => [
                            'match' => 'main',
                            'value' => 'main',
                            'nodes' => [
                                'action' => [
                                    'match' => 'index',
                                    'value' => 'index',
                                ]
                            ]
                        ]
                    ]
                ],
                'module2' => [
                    'match' => 'site',
                    'value' => 'site',
                    'nodes' => [
                        'controller' => [
                            'match' => 'main',
                            'value' => 'main',
                            'nodes' => [
                                'action' => [
                                    'match' => 'index',
                                    'value' => 'index',
                                ]
                            ]
                        ]
                    ]
                ],
                'module3' => [
                    'match' => 'site',
                    'value' => 'site',
                    'nodes' => [
                        'controller' => [
                            'match' => 'main',
                            'value' => 'main',
                            'nodes' => [
                                'action' => [
                                    'match' => 'index',
                                    'value' => 'index',
                                ]
                            ]
                        ]
                    ]
                ],
                'module4' => [
                    'match' => 'site',
                    'value' => 'site',
                    'nodes' => [
                        'controller' => [
                            'match' => 'main',
                            'value' => 'main',
                            'nodes' => [
                                'action' => [
                                    'match' => 'index',
                                    'value' => 'index',
                                ]
                            ]
                        ]
                    ]
                ],
                'module5' => [
                    'match' => 'site',
                    'value' => 'site',
                    'nodes' => [
                        'controller' => [
                            'match' => 'main',
                            'value' => 'main',
                            'nodes' => [
                                'action' => [
                                    'match' => 'index',
                                    'value' => 'index',
                                ]
                            ]
                        ]
                    ]
                ],
            ],
            "defaults" => [
                "lang" => "en",
                "module" => "site",
                "controller" => "main",
                "action" => "index",
                "page" => "en",
            ]
        ];
        echo '<pre>';
        $time = microtime( true );
        ConfigHandlerFactory::create( $filePhp )->setFullConfig( $data )->save();
        echo 'Php create #1 time: ', microtime( true ) - $time, '<br/>';

        $time = microtime( true );
        ConfigHandlerFactory::create( $fileJson )->setFullConfig( $data )->save();
        echo 'Json create #1 time: ', microtime( true ) - $time, '<br/>';

        $time = microtime( true );
        ConfigHandlerFactory::create( $fileIni )->setFullConfig( $data )->save();
        echo 'Ini create #1 time: ', microtime( true ) - $time, '<br/>';

        $time = microtime( true );
        for ( $i = 1; $i < 100; $i++ ) {
            $r = ConfigHandlerFactory::create( $filePhp )->getFullConfig();
        }
        echo 'Php read #100 time: ', microtime( true ) - $time, '<br/>';

        $time = microtime( true );
        for ( $i = 1; $i < 100; $i++ ) {
            $r = ConfigHandlerFactory::create( $fileJson )->getFullConfig();
        }
        echo 'Json read #100 time: ', microtime( true ) - $time, '<br/>';

        $time = microtime( true );
        for ( $i = 1; $i < 100; $i++ ) {
            $r = ConfigHandlerFactory::create( $fileIni )->getFullConfig();
        }
        echo 'Ini read #100 time: ', microtime( true ) - $time, '<br/>';

        echo '</pre>';
    }

    public function actionLang()
    {
        $file = DIR_USF . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'languages.json';
        $handler = \Usf\Core\Base\Factories\ConfigHandlerFactory::create($file);
        $languages = $handler->getFullConfig();
        $db = db();
        $tableName = $db->getPrefix() . 'languages';
        $sql = "truncate table prefix__languages; insert into prefix__languages(`code2`, `name`, `native_name`, `is_active`) values";
        $values = [];
        foreach ( $languages as $code => $names ) {
            $values[] = '(' . implode(',', [ "'$code'", "'{$names[ 'name' ]}'", "'{$names[ 'nativeName' ]}'", in_array( $names[ 'name' ], ['Ukrainian', 'English'] ) ? 1 : 0 ] ) . ')';
        }
        $sql .= implode( ',', $values ) . ";";
        echo '<pre>';
        $result = $db->query($sql);
        var_dump($result);
        var_dump($db->lastInsertId($tableName));
        echo microtime( true ) - usf()->getStartTime();
        echo '<pre>';
    }
}