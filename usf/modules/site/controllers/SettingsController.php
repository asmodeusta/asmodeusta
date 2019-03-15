<?php

use Usf\Core\Base\Controller;

class SettingsController extends Controller
{
    public function actionIndex( $page = 1 )
    {
        echo '<pre>';
        var_dump( usf()->router );
        echo microtime( true ) - usf()->usfStartTime;
        echo '<pre>';
    }
}