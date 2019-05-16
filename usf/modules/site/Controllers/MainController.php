<?php

namespace Usf\Site\Controllers;

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
            echo '<ul>';
            foreach ($words as $word) {
                echo '<li>' . $word . '</li>';
            }
            echo '</ul>';
        }
    }

    protected function generateWords(string $symbols, int $num)
    {
        $symbols = mb_str_split($symbols);
        $words = [];
        foreach ($this->generateWord($symbols, $num) as $word) {
            $words[] = $word;
        }
        return array_unique($words);
    }

    protected function generateWord(array $symbols, int $num, string $word = '')
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