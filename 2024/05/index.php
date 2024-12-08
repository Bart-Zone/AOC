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
    $lineBreak = false;
    $rules = [];
    $updates = [];
    foreach ($input as $line) {
        if (!$lineBreak && empty($line)) {
            $lineBreak = true;
            continue;
        }
        if (!$lineBreak) {
            $pageSequence = explode('|', $line);
            $rules[$pageSequence[0]][] = $pageSequence[1];
        } else {
            $updates[] = explode(',', $line);
        }
    }

    $result = 0;

    foreach ($updates as $pages) {
        $pagesCount = count($pages);
        foreach ($pages as $pos => $page) {
            $after = array_slice($pages, $pos + 1);
            $isset = isset($rules[$page]);
            if ((!$isset && $pos + 1 !== $pagesCount) || $isset && !empty(array_diff($after, $rules[$page]))) {
                continue 2;
            }
        }
        $middleKey = ceil($pagesCount / 2) - 1;
        $result += $pages[$middleKey];
    }

    echo $result;
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
    $lineBreak = false;
    $rules = [];
    $updates = [];
    foreach ($input as $line) {
        if (!$lineBreak && empty($line)) {
            $lineBreak = true;
            continue;
        }
        if (!$lineBreak) {
            $pageSequence = explode('|', $line);
            $rules[$pageSequence[0]][] = $pageSequence[1];
        } else {
            $updates[] = explode(',', $line);
        }
    }

    $result = 0;

    foreach ($updates as $pages) {
        $pagesCount = count($pages);
        if (!checkOrder($pages, $rules)) {
            sortOrder($pages, $rules);
            $middleKey = ceil($pagesCount / 2) - 1;
            $result += $pages[$middleKey];
        }
    }

    echo $result;
}

function checkOrder($pages, $rules)
{
    $pagesCount = count($pages);
    foreach ($pages as $pos => $page) {
        $after = array_slice($pages, $pos + 1);
        $isset = false;
        if (isset($rules[$page])) {
            $isset = true;
            $diff = array_diff($after, $rules[$page]);
        }
        if ((!$isset && $pos + 1 !== $pagesCount) || $isset && !empty($diff)) {
            return false;
        }
    }
    return true;
}

function sortOrder(array &$pages, $rules)
{
    foreach ($pages as $page) {
        $results[$page] = array_diff($pages, $rules[$page]);
    }
    uksort($results, function ($item1, $item2) use($results) {
        return count($results[$item1]) <=> count($results[$item2]);
    });

    $pages = array_keys($results);
}