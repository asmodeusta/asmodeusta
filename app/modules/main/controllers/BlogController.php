<?php

class BlogController extends Controller
{

    protected $module = "main";
    protected $controller = "blog";

    protected function actionIndex($year = 0, $month = 0, $day = 0) {
        echo "$year.$month.$day";
        
        ?>
        <div class="heart"></div>
        <style>
            . heart {
                display: inline-block;
                height: 50px;
                width: 50px;
                
                background-color: red;
            }
        </style>
        <?php
    }

}