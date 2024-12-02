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

    foreach ($example as $report) {
        $levels = explode(' ', $report);
        $safe = true;
        $firstLevel = array_slice($levels, 0, 1);
        foreach ($levels as $level) {
//            if (abs($previousLevel )
        }
    }
}