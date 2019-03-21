<?php

class UserController extends Controller
{

    protected $module = "site";
    protected $controller = "user";

    protected function actionIndex($id = 0) {
        echo "<pre>";
        var_dump(App()->router->createUrl(["module"=>"site","controller"=>"user","action"=>"view","id"=>1]));
        echo "</pre>";

        return true;
    }

}