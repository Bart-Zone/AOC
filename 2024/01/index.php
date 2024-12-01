<?php

require_once('../inputReader.php');

if (isset($_GET['part']) && $_GET['part'] == '2') {
    part02();
} else {
    part01();
}

function part01() {
    $input = getFileContent('input');
    $example = getFileContent('example');
    $firstColumn = [];
    $secondColumn = [];
    foreach ($input as $line) {
        preg_match_all('/\d+/', $line, $result);
        $firstColumn[] = $result[0][0];
        $secondColumn[] = $result[0][1];
    }

    sort($firstColumn);
    sort($secondColumn);
    $result = 0;
    foreach ($firstColumn as $key => $value) {
        $result += abs($value - $secondColumn[$key]);
    }

    echo $result;
}

function part02() {
    $input = getFileContent('input');
    $example = getFileContent('example');

    $firstColumn = [];
    $secondColumn = [];


    foreach ($input as $line) {
        preg_match_all('/\d+/', $line, $result);
        $firstColumn[] = intval($result[0][0]);
        $secondColumn[] = intval($result[0][1]);
    }

    $result = 0;
    foreach ($firstColumn as $value) {
        $secondValues = array_filter($secondColumn, function(int $item) use ($value){
            return $value === $item;
        });
        $result += ($value * count($secondValues));
    }

    echo $result;
}