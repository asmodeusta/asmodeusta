<?php

namespace Usf\Site\Controllers;

use Usf\Base\Factories\ConfigHandlerStaticFactory;
use Usf\Base\Controller;

class SettingsController extends Controller
{
    public function actionIndex()
    {
        
    }

    public function actionGuid()
    {
        
    }

    public function actionCheck()
    {

    }

    public function actionLang()
    {
        if ( isset( $_POST[ 'submit' ] ) ) {
            $file = DIR_APP . DS . 'data' . DS . 'languages.json';
            $handler = ConfigHandlerStaticFactory::create($file);
            $languages = $handler->getFullConfig();
            $db = db();
            $sql = "truncate table usf_languages;
                insert into usf_languages(`code2`, `name`, `native_name`, `is_active`) 
                values";
            $values = [];
            foreach ($languages as $code => $names) {
                $values[] = '(' . implode(',', [
                        "'$code'",
                        "'{$names[ 'name' ]}'",
                        "'{$names[ 'nativeName' ]}'",
                        in_array($names['name'], ['Ukrainian', 'English']) ? 1 : 0
                    ]) . ')';
            }
            $sql .= implode(',', $values) . ";";
            echo '<pre>';
            if ( $result = $db->query($sql) ) {
                echo 'Languages imported successfully';
            } else {
                echo 'Import error!';
            }
            echo '<pre>';
        }
        include $this->module->getDirectory() . DS . 'templates/settings/lang.php';
    }
}