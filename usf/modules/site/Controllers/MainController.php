<?php

namespace Usf\Site\Controllers;

use function Combinatorics\combination;
use function Combinatorics\partitions;
use Usf\Base\Controller;
use Usf\Site\Views\MainHeartView;

class MainController extends Controller
{

    public function actionIndex($page = 1)
    {
        echo 'This is page #' . $page;
    }

    public function actionFact($num=1)
    {
        if (is_array($num)) {
            $numArr = $num;
            foreach ($numArr as $num) {
                $fact = factorial($num);
                $part = null;
                $comb = null;
                if ($num > 3) {
                    $part = partitions($num, $num-2);
                    $comb = combination($num, $num-2);
                }
                echo $num . '! = ' . $fact;
                echo is_null($part) ? '' : '; A(' . $num . ', ' . ($num-2) . ') = ' . $part;
                echo is_null($comb) ? '' : '; C(' . $num . ', ' . ($num-2) . ') = ' . $comb;
                echo '<br/>';
            }
        } else {
            if (isset($_POST)
                && array_key_exists('num', $_POST)) {
                $num = $_POST['num'];
            }
            $fact = factorial($num);
            echo $num . '! = ' . $fact . '<br/>';
        }
        ?>
        <form method="post">
            <input type="number" name="num" value="<?php echo $num; ?>" min="1" max="25">
            <input type="submit" value="Generate">
        </form>
        <?php
    }

    public function actionUpdate()
    {

    }

    public function actionAdd()
    {
        $newRoute = [
            'name' => 'lang',
            'match' => '(en|ua)',
            'value' => '$1',
            'nodes' => [
                [
                    'name' => 'module',
                    'match' => 'site',
                    'value' => 'site',
                    'nodes' => [
                        [
                            'name' => 'controller',
                            'match' => 'settings',
                            'value' => 'settings',
                            'nodes' => [
                                [
                                    'name' => 'action',
                                    'match' => 'float',
                                    'value' => 'float',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        echo '<pre>';
        if (router()->addRoute($newRoute)) {
            echo 'success!';
        } else {
            echo 'failed!';
        }
        echo '</pre>';
    }

    public function actionHeart()
    {
        new MainHeartView($this->module);
    }

}