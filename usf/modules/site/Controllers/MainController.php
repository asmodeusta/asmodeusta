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
        $symbols = '';
        $num = 1;
        if (isset($_POST)
            && array_key_exists('symbols',$_POST)
            && array_key_exists('num', $_POST)) {
            $symbols = $_POST['symbols'];
            $num = $_POST['num'];
            $words = $this->generateWords($symbols, $num);
        }
        ?>
        <form method="post">
            <input type="text" name="symbols" value="<?php echo $symbols; ?>" minlength="2" maxlength="10">
            <input type="number" name="num" value="<?php echo $num; ?>" min="1" max="10">
            <input type="submit" value="Generate">
        </form>
        <?php
        if (isset($words)) {
            echo '<ol>';
            foreach ($words as $word) {
                echo '<li>' . $word . '</li>';
            }
            echo '</ol>';
        }
        $arr1 = ['1' => 1, '2' => 2, '3' => 3];
        $arr2 = ['1' => 4, '2' => 5, '3' => 6];
        $arr1 += $arr2;
        var_dump($arr1);
    }

    protected function generateWords(string $symbols, int $num)
    {
        $symbols = mb_str_split($symbols);
        $words = [];
        foreach ($this->generateWord($symbols, $num) as $word) {
            $words[] = $word;
        }
        $result = array_unique($words);
        sort($result);
        return $result;
    }

    protected function generateWord(array $symbols, int $num, $word = '')
    {
        foreach ($symbols as $symbol) {
            $pos = array_search($symbol, $symbols);
            if (mb_strlen($word)+1 >= $num) {
                yield $word . $symbol;
            } else {
                $newSymbols = $symbols;
                unset($newSymbols[$pos]);
                foreach ($this->generateWord(
                    $newSymbols,
                    //array_filter($symbols, function ($element) use ($symbol) {return $symbol !== $element; }),
                    $num,
                    $word . $symbol
                ) as $currWord) {
                    yield $currWord;
                }
            }
        }
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