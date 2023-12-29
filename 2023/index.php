<?php

require_once('../fileReader.php');

function day1()
{
    $input = getFileContent('01');
    $numberWords = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
    $replacements = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    $pattern = implode('|', array_merge($numberWords, $replacements));
    $reversedPattern = strrev($pattern);
    $total = 0;
    foreach ($input as $row) {
        $reversedRow = strrev($row);
        preg_match('/' . $pattern . '/', $row, $first);
        preg_match('/' . $reversedPattern . '/', $reversedRow, $last);
        $firstD = str_replace($numberWords, $replacements, reset($first));
        $lastD = str_replace($numberWords, $replacements, strrev(reset($last)));
        $sum = (int)$firstD * 10 + (int)$lastD;
        $total += $sum;
    }
    return $total;
}

function day2()
{
    $input = getFileContent('02');
    $total = 0;
    $totalP2 = 0;
    foreach ($input as $row) {
        preg_match('/[0-9]+/', $row, $result);
        $gameId = (int)reset($result);
        $cubes = explode(':', $row);
        preg_match(
            '/[2-9]{1}[0-9]+|[0-9]{3,}|[1]{1}[3-9]{1}\\sred|[1]{1}[4-9]{1}\\sgreen|[1]{1}[5-9]{1}\\sblue/',
            $cubes[1],
            $cubes
        );
        preg_match_all('/[0-9]+\sred|[0-9]+\sgreen|[0-9]+\sblue/', $row, $fewestCubes);
        $allCubes = reset($fewestCubes);
        $maxFewest = [];
        foreach ($allCubes as $cube) {
            $cubeSet = explode(' ', $cube);
            if (!isset($maxFewest[$cubeSet[1]]) || $maxFewest[$cubeSet[1]] < (int)$cubeSet[0]) {
                $maxFewest[$cubeSet[1]] = (int)$cubeSet[0];
            }
        }
        $totalP2 += array_product($maxFewest);
        if (empty($cubes)) {
            $total += $gameId;
        }
    }
    return 'Day 02:<br>Part 1:' . $total . '<br>' . 'Part2: ' . $totalP2 . '<br>';
}

function day3()
{
    $input = getFileContent('03');
    $maxRows = count($input) - 1;
    $result = 0;
    $total = 0;
    foreach ($input as $y => $row) {
        $currentRowNumbers = [];
        $currentRowSymbols = [];
        $nextRowNumbers = [[]];

        preg_match_all('/[\\d]+/', $row, $currentRowNumbers, PREG_OFFSET_CAPTURE);
        preg_match_all('/[^\\d^\\w^\\.]+/', $row, $currentRowSymbols, PREG_OFFSET_CAPTURE);

        preg_match_all('/\\*/', $row, $possibleGears, PREG_OFFSET_CAPTURE);
        $prevRowNumbers = [[]];
        //to check
        // 1. Step same Row
//        $uncheckedNumbers = checkNumberTouchingSymbol($currentRowNumbers[0], $currentRowSymbols[0], $result);
        // previous Row
        if ($y > 0  //&& !empty($uncheckedNumbers)
        ) {
            $prevRow = $input[$y - 1];
//            preg_match_all('/[^\\d^\\w^\\.]+/', $prevRow , $prevRowSymbols, PREG_OFFSET_CAPTURE);
            preg_match_all('/[\\d]+/', $prevRow, $prevRowNumbers, PREG_OFFSET_CAPTURE);
//            $uncheckedNumbers = checkNumberTouchingSymbol($uncheckedNumbers, $prevRowSymbols[0], $result);

        }

        if ($y < $maxRows
            //    && !empty($uncheckedNumbers)
        ) {
            // next Row
            $nextRow = $input[$y + 1];
//            preg_match_all('/[^\\d^\\w^\\.]+/', $nextRow, $nextRowSymbols, PREG_OFFSET_CAPTURE);
            preg_match_all('/[\\d]+/', $nextRow, $nextRowNumbers, PREG_OFFSET_CAPTURE);
//            checkNumberTouchingSymbol($uncheckedNumbers, $nextRowSymbols[0], $result);
        }

        $rowSum = checkIfIsGear($possibleGears[0], $prevRowNumbers[0], $currentRowNumbers[0], $nextRowNumbers[0]);
        $total += $rowSum;
    }

    return $total;
}

function checkNumberTouchingSymbol(array $allNumbers, array $symbols, &$result)
{
    if (empty($symbols)) {
        return $allNumbers;
    }

    $uncheckedNumbers = $allNumbers;
    foreach ($allNumbers as $key => $numbersSet) {
        $digitsLength = strlen($numbersSet[0]);
        $range = [$numbersSet[1] - 1, $numbersSet[1] + $digitsLength];
        foreach ($symbols as $symbolSet) {
            if ($range[0] <= $symbolSet[1] && $symbolSet[1] <= $range[1]) {
                $result += $numbersSet[0];
                unset($uncheckedNumbers[$key]);
                continue 2;
            }
        }
    }

    return $uncheckedNumbers;
}

function checkIfIsGear(array $gears, array $prevRow, array $currentRow, array $nextRow)
{
    $total = 0;
    foreach ($gears as $gear) {
        //same row, number is before and after gear
        $unchecked = checkNumberTouchingSymbol($currentRow, [$gear], $result);
        $checked2 = array_diff_key($currentRow, $unchecked);
        if (count($checked2) == 2) {
            $total += array_values($checked2)[0][0] * array_values($checked2)[1][0];
            continue;
        }

        $unchecked = checkNumberTouchingSymbol($prevRow, [$gear], $result);
        $checked = array_diff_key($prevRow, $unchecked);

        // gear touch one in pre Row and one in current row
        if (!empty($checked) && count($checked) == 2) {
            $total += array_values($checked)[0][0] * array_values($checked)[1][0];
            continue;
        } elseif (!empty($checked) && !empty($checked2)) {
            $total += array_values($checked)[0][0] * array_values($checked2)[0][0];
            continue;
        }


        $unchecked = checkNumberTouchingSymbol($nextRow, [$gear], $result);
        $checked3 = array_diff_key($nextRow, $unchecked);
        $checked4 = array_merge($checked, $checked2);
        if (!empty($checked3) && count($checked3) == 2) {
            $total += array_values($checked3)[0][0] * array_values($checked3)[1][0];
        } elseif (!empty($checked3) && !empty($checked4)) {
            $total += array_values($checked3)[0][0] * array_values($checked4)[0][0];
        }
    }

    return $total;
}

function day4()
{
    $input = getFileContent('04');
    $total = 0;
    $todo = [];
    foreach ($input as $row) {
        [$cardNumberName, $cards] = explode(':', $row);
        preg_match('/[\\d]+/', $cardNumberName, $cardNumber);
        $cardId = (int)reset($cardNumber);
        [$winningCardsSets, $myCardsSets] = explode('|', $cards);
        preg_match_all('/[\\d]+/', $winningCardsSets, $winningCards);
        preg_match_all('/[\\d]+/', $myCardsSets, $myCards);
        $winning = getCards($winningCardsSets);
        $my = getCards($myCardsSets);
        $matches = array_intersect($winning, $my);
        $matchesAmount = count($matches);
        $cardMatrix[$cardId] = [$winning, $my, 'matches' => $matchesAmount, 'touches' => 0];
        //p1
//        $total += floor(pow(2, $matchesAmount - 1));
        $todo[] = $cardId;
    }

    foreach ($todo as $card) {
        for ($j = 0; $j <= $cardMatrix[$card]['touches']; $j++) {
            for ($i = 1; $i <= $cardMatrix[$card]['matches']; $i++) {
                $cardMatrix[$card + $i]['touches']++;
            }
        }
        $cardMatrix[$card]['touches']++;
        $total += $cardMatrix[$card]['touches'];
    }

    return $total;
}

function getCards(string $cardSet)
{
    preg_match_all('/[\\d]+/', $cardSet, $cards);

    return reset($cards);
}

//echo day1();
//echo day2();
//echo day3();
echo day4();