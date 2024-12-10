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

    $operators = ['+', '*'];

    foreach ($input as $line) {
        [$result, $params] = explode(':', $line);
        preg_match_all('/\d+/', $params, $paramMatches);
        $parameter = reset($paramMatches);

    }
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
}