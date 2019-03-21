<?php

class MainController extends Controller
{

    protected $module = "site";
    protected $controller = "user";

    protected function actionIndex() {
        $fighter = new Fighter();
        echo "<pre>";
        var_dump($fighter);
        echo "</pre>";
    }

}