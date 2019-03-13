<?php

use Usf\Core\Base\Controller;

class MainController extends Controller
{

    public function actionIndex( $page = 1 )
    {
        var_dump( $page );
    }

}