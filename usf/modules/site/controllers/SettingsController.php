<?php

namespace Usf\Site\Controllers;

use Usf\Core\Base\Controller;

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
        echo microtime( true ) - usf()->getStartTime();

        echo '<pre>';
    }

    public function actionGuid()
    {
        $code = random_bytes ( 32 );
        $code64 = base64_encode( $code );
        $code16 = bin2hex( $code );
        echo $code, ' :(', mb_strlen( $code ), ')<br/>';
        echo $code64, ' :(', mb_strlen( $code64 ), ')<br/>';
        echo $code16, ' :(', mb_strlen( $code16 ), ')<br/>';

        var_dump( $_SERVER[ 'SERVER_NAME' ] );
        var_dump( $_SERVER[ 'HTTP_HOST' ] );
    }

    public function actionCheck()
    {

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