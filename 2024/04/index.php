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

    }
    echo $endResult;
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
    $endResult = 0;
    foreach ($input as $line) {

    }

    echo $endResult;
}