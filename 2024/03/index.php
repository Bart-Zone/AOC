<?php

require_once('../inputReader.php');

if (isset($_GET['part']) && $_GET['part'] == '2') {
    part02();
} else {
    part01();
}

function part01()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
    $endResult = 0;
    foreach ($input as $line) {
        preg_match_all('/mul\(\d{1,3},\d{1,3}\)/', $line, $result);
        $results = reset($result);
        foreach ($results as $mul) {
            preg_match_all('/\d+/', $mul, $digits);
            $digitPair = reset($digits);
            $endResult += intval($digitPair[0]) * intval($digitPair[1]);
        }
    }
    echo $endResult;
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
    $endResult = 0;
    foreach ($input as $line) {
        $chunks = preg_split('/don\'t\(\)/', $line);
        $dos[] = array_shift($chunks);
        foreach ($chunks as $chunk) {
            $doChunks = preg_split('/do\(\)/', $chunk);
            $trash = array_shift($doChunks);
            $dos = array_merge($dos, $doChunks);
        }
    }

    foreach ($dos as $do) {
        preg_match_all('/mul\(\d{1,3},\d{1,3}\)/', $do, $result);
        $res = reset($result);
        foreach ($res as $mul) {
            preg_match_all('/\d+/', $mul, $digits);
            $digitPair = reset($digits);
            $endResult += intval($digitPair[0]) * intval($digitPair[1]);
        }
    }

    echo $endResult;
}