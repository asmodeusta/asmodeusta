<?php

namespace Usf\Site\Controllers;

use Usf\Core\Base\Controller;

class SettingsController extends Controller
{
    public function actionIndex()
    {
        echo '<pre>';
        var_dump( usf()->router );
        echo microtime( true ) - usf()->startTime;
        echo '<pre>';
    }

    public function actionGuid()
    {
        echo uniqid( '', true );
    }

    public function actionCheck()
    {
        echo '<pre>';
        var_dump($_SERVER);
        echo '</pre>';
    }

    public function actionLang()
    {
        $file = DIR_USF . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'languages.json';
        $handler = \Usf\Core\Base\Factories\ConfigHandlerFactory::create($file);
        $langs = $handler->getFullConfig();
        $db = db();
        $tableName = $db->getPrefix() . 'languages';
        $sql = "truncate table prefix__languages; insert into prefix__languages(`code2`, `name`, `native_name`, `is_active`) values";
        $values = [];
        foreach ( $langs as $code => $names ) {
            $values[] = '(' . implode(',', [ "'$code'", "'{$names[ 'name' ]}'", "'{$names[ 'nativeName' ]}'", in_array( $names[ 'name' ], ['Ukrainian', 'English'] ) ? 1 : 0 ] ) . ')';
        }
        $sql .= implode( ',', $values ) . ";";
        echo '<pre>';
        $result = $db->query($sql);
        var_dump($result);
        var_dump($db->lastInsertId($tableName));
        echo microtime( true ) - usf()->startTime;
        echo '<pre>';
    }
}