<?php

class UserController extends Controller
{

    protected $module = "main";
    protected $controller = "user";

    protected function actionIndex($id = 0) {
        echo "<pre>";
        var_dump(App()->router->createUrl(["module"=>"main","controller"=>"user","action"=>"view","id"=>1]));
        echo "</pre>";

        return true;
    }

}