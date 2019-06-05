<?php

namespace Combinatorics;

function permutation(int $n) : int
{
    return factorial($n);
}

function partitions(int $n, int $m) : int
{
    return $n >= $m ? factorial($n) / factorial($n - $m) : 0;
}

function combination(int $n, int $m) : int
{
    return $n >= $m ? partitions($n, $m) / factorial($m) : 0;
}