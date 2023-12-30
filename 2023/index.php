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

function day5()
{
    $input = getFileContent('05');
    preg_match_all('/[\\d]+/', $input[0], $seeds);
    preg_match_all('/[\\d]+ [\\d]+/', $input[0], $seedPairs);
    $seeds = reset($seeds);
    $seedPairs = reset($seedPairs);
    $seedToSoilMapStartKey = array_search('seed-to-soil map:', $input) + 1;
    $soilToFertilizerMapStartKey = array_search('soil-to-fertilizer map:', $input) + 1;
    $fertilizerToWaterMapKeyStart = array_search('fertilizer-to-water map:', $input) + 1;
    $waterToLightMapStartKey = array_search('water-to-light map:', $input) + 1;
    $lightToTemperatureMapKeyStart = array_search('light-to-temperature map:', $input) + 1;
    $temperatureToHumidityMapKeyStart = array_search('temperature-to-humidity map:', $input) + 1;
    $humidityToLocationMapKeyStart = array_search('humidity-to-location map:', $input) + 1;
    $seedToSoil = array_slice(
        $input,
        $seedToSoilMapStartKey,
        $soilToFertilizerMapStartKey - $seedToSoilMapStartKey - 2
    );
    $soilToFertilizer = array_slice(
        $input,
        $soilToFertilizerMapStartKey,
        $fertilizerToWaterMapKeyStart - $soilToFertilizerMapStartKey - 2
    );
    $fertilizerToWater = array_slice(
        $input,
        $fertilizerToWaterMapKeyStart,
        $waterToLightMapStartKey - $fertilizerToWaterMapKeyStart - 2
    );
    $waterToLight = array_slice(
        $input,
        $waterToLightMapStartKey,
        $lightToTemperatureMapKeyStart - $waterToLightMapStartKey - 2
    );
    $lightToTemperature = array_slice(
        $input,
        $lightToTemperatureMapKeyStart,
        $temperatureToHumidityMapKeyStart - $lightToTemperatureMapKeyStart - 2
    );
    $temperatureToHumidity = array_slice(
        $input,
        $temperatureToHumidityMapKeyStart,
        $humidityToLocationMapKeyStart - $temperatureToHumidityMapKeyStart - 2
    );
    $humidityToLocation = array_slice($input, $humidityToLocationMapKeyStart);
    $maps = [];
    $seedToSoilMap = [];
    foreach ($seedToSoil as $row) {
        [$destinationStart, $start, $steps] = explode(' ', $row);
        $seedToSoilMap[] = [
            'name' => 'seedToSoil',
            'start' => (int)$start,
            'end' => $start + $steps - 1,
            'destiStart' => (int)$destinationStart,
            'destiEnd' => $destinationStart + $steps - 1,
            'steps' => (int)$steps
        ];
    }
    $maps[] = $seedToSoilMap;

    $soilToFertilizerMap = [];
    foreach ($soilToFertilizer as $row) {
        [$destinationStart, $start, $steps] = explode(' ', $row);
        $soilToFertilizerMap[] = [
            'name' => 'soilToFertilizer',
            'start' => (int)$start,
            'end' => $start + $steps - 1,
            'destiStart' => (int)$destinationStart,
            'destiEnd' => $destinationStart + $steps - 1,
            'steps' => (int)$steps
        ];
    }
    $maps[] = $soilToFertilizerMap;

    $fertilizerToWaterMap = [];
    foreach ($fertilizerToWater as $row) {
        [$destinationStart, $start, $steps] = explode(' ', $row);
        $fertilizerToWaterMap[] = [
            'name' => 'fertilizerToWater',
            'start' => (int)$start,
            'end' => $start + $steps - 1,
            'destiStart' => (int)$destinationStart,
            'destiEnd' => $destinationStart + $steps - 1,
            'steps' => (int)$steps
        ];
    }
    $maps[] = $fertilizerToWaterMap;

    $waterToLightMap = [];
    foreach ($waterToLight as $row) {
        [$destinationStart, $start, $steps] = explode(' ', $row);
        $waterToLightMap[] = [
            'name' => 'waterToLight',
            'start' => (int)$start,
            'end' => $start + $steps - 1,
            'destiStart' => (int)$destinationStart,
            'destiEnd' => $destinationStart + $steps - 1,
            'steps' => (int)$steps
        ];
    }
    $maps[] = $waterToLightMap;

    $lightToTemperatureMap = [];
    foreach ($lightToTemperature as $row) {
        [$destinationStart, $start, $steps] = explode(' ', $row);
        $lightToTemperatureMap[] = [
            'name' => 'lightToTemperature',
            'start' => (int)$start,
            'end' => $start + $steps - 1,
            'destiStart' => (int)$destinationStart,
            'destiEnd' => $destinationStart + $steps - 1,
            'steps' => (int)$steps
        ];
    }
    $maps[] = $lightToTemperatureMap;

    $temperatureToHumidityMap = [];
    foreach ($temperatureToHumidity as $row) {
        [$destinationStart, $start, $steps] = explode(' ', $row);
        $temperatureToHumidityMap[] = [
            'name' => 'temperatureToHumidity',
            'start' => (int)$start,
            'end' => $start + $steps - 1,
            'destiStart' => (int)$destinationStart,
            'destiEnd' => $destinationStart + $steps - 1,
            'steps' => (int)$steps
        ];
    }
    $maps[] = $temperatureToHumidityMap;

    $humidityToLocationMap = [];
    foreach ($humidityToLocation as $row) {
        [$destinationStart, $start, $steps] = explode(' ', $row);
        $humidityToLocationMap[] = [
            'name' => 'humidityToLocation',
            'start' => (int)$start,
            'end' => $start + $steps - 1,
            'destiStart' => (int)$destinationStart,
            'destiEnd' => $destinationStart + $steps - 1,
            'steps' => (int)$steps
        ];
    }
    $maps[] = $humidityToLocationMap;
    $usedLastPointer = [];

//    foreach ($seeds as $seed) {
    foreach ($seedPairs as $seedPair) {
//    $pointer = $seed;

        [$pointerStart, $steps] = explode(' ', $seedPair);
        $pointerEnd = $pointerStart + $steps - 1;
        $pointers = [['start' => (int)$pointerStart, 'end' => (int)$pointerEnd]];
        foreach ($maps as $map) {
            $intersection = [];
            $leftSubset = [];
            $rightSubset = [];
            $pointersCopy = array_unique($pointers, SORT_REGULAR);
            $pointers = [];
            while ($pointer = array_pop($pointersCopy)) //            foreach ($pointersCopy as $pointer) {
            {
                $noMatch = true;

                foreach ($map as $entry) {
//                if ($pointer >= $entry['start'] && $pointer <= $entry['end']) {
//                    $diff = $pointer - $entry['start'];
//                    $pointer = $entry['destiStart'] + $diff;
//                    continue 2;
//                }
                    getIntersection($pointer, $entry, $intersection, $leftSubset, $rightSubset);

                    if (!empty($intersection)) {
                        $startDiff = $intersection['start'] - $entry['start'];
                        $endDiff = $intersection['end'] - $entry['end'];
                        $pointers[] = [
                            'start' => $entry['destiStart'] + $startDiff,
                            'end' => $entry['destiEnd'] + $endDiff
                        ];
                        $noMatch = false;
                    }
                    if (!empty($leftSubset)) {
                        $pointersCopy[] = [
                            'start' => $leftSubset['start'],
                            'end' => $leftSubset['end']
                        ];
                    }
                    if (!empty($rightSubset)) {
                        $pointersCopy[] = [
                            'start' => $rightSubset['start'],
                            'end' => $rightSubset['end']
                        ];
                    }
                }
                if ($noMatch) {
                    $pointers[] = $pointer;
                }
            }
        }
        $usedLastPointer[] = $pointers;
//        $usedLastPointer[] = $pointerStart;

    }
    $lowestPointer = [];
    foreach ($usedLastPointer as $pointer) {
        foreach ($pointer as $entry) {
            $lowestPointer[] = $entry['start'];
        }
    }
    sort($lowestPointer);
    return $lowestPointer[0];
}

function getIntersection(
    array $leftRange,
    array $rightRange,
    array &$intersect,
    array &$leftSubset,
    array &$rightSubset
) {
    $intersect = [];
    $leftSubset = [];
    $rightSubset = [];

    if ($leftRange['start'] < $rightRange['start']) {
        if ($leftRange['end'] < $rightRange['start']) {
            // no intersection
//            $leftSubset = $leftRange;
        } elseif ($leftRange['end'] <= $rightRange['end']) {
            // subset on left side and intersection
            $leftSubset['start'] = $leftRange['start'];
            $leftSubset['end'] = $rightRange['start'] - 1;
            $intersect['start'] = $rightRange['start'];
            $intersect['end'] = $leftRange['end'];
        } else {
            //subset on left side intersection and subset on right side
            $leftSubset['start'] = $leftRange['start'];
            $leftSubset['end'] = $rightRange['start'] - 1;
            $intersect['start'] = $rightRange['start'];
            $intersect['end'] = $rightRange['end'];
            $rightSubset['start'] = $rightRange['end'] + 1;
            $rightSubset['end'] = $leftRange['end'];
        }
    } else {
        if ($leftRange['start'] > $rightRange['end']) {
            //no intersection,
//            $leftSubset = $leftRange;
        } elseif ($leftRange['end'] <= $rightRange['end']) {
            // intersection
            $intersect = $leftRange;
        } else {
            //intersection and right subset
            $intersect['start'] = $leftRange['start'];
            $intersect['end'] = $rightRange['end'];
            $rightSubset['start'] = $rightRange['end'] + 1;
            $rightSubset['end'] = $leftRange['end'];
        }
    }
}

function day6()
{
    $input = getFileContent('06');

//    preg_match_all('/[\\d]+/', $input[0], $times);
//    preg_match_all('/[\\d]+/', $input[1], $distances);
//    $times = reset($times);
//    $distances = reset($distances);
    $times = [preg_filter('/[^\\d]+/', '', $input[0])];
    $distances = [preg_filter('/[^\\d]+/', '', $input[1])];
    $allPossibility = [];
    foreach ($times as $race => $time) {
        $possibilities = $time + 1 - 2;
        $validPossibilities = 0;
        for ($i = 1; $i <= $possibilities / 2; $i++) {
            $millSec = $i * ($time - $i);
            if ($millSec > $distances[$race]) {
                $validPossibilities++;
            }
        }
        $allPossibility[] = $validPossibilities * 2 + ($time + 1) % 2;
    }

    return array_product($allPossibility);
}

function day7()
{
    $input = getFileContent('07');
    $onePair = [];
    $twoPair = [];
    $three = [];
    $four = [];
    $five = [];
    $highCard = [];
    $fullHouse = [];
    $sequenz = [
        'A' => 55,
        'K' => 45,
        'Q' => 35,
        'J' => 1,
        'T' => 15
    ];

    foreach ($input as $row) {
        [$cardHand, $bid] = explode(' ', $row);
        $cards = str_split($cardHand);
        $diffValues = array_count_values($cards);
        arsort($diffValues);
        foreach ($cards as &$card) {
            if (ord($card) >= 65) {
                $card = $sequenz[$card];
            }
            $card = (int)$card;
        }
        $differentCards = count(array_unique($cards));
        if (in_array(1, $cards) && count($diffValues) > 1) {
            $differentCards--;
            $plusAmount = $diffValues['J'];
            unset($diffValues['J']);
            arsort($diffValues);
            $maxCard = key($diffValues);
            $diffValues[$maxCard] += $plusAmount;
        }
        switch ($differentCards) {
            case 1: //five of Card
                $five[$bid] = $cards;
                break;
            case 2: // four of a kind or fullhouse
                if (max($diffValues) == 3) {
                    $fullHouse[$bid] = $cards;
                } else {
                    $four[$bid] = $cards;
                }
                break;
            case 3: // two pairs or three of a kind
                if (max($diffValues) == 3) {
                    $three[$bid] = $cards;
                } else {
                    $twoPair[$bid] = $cards;
                }
                break;
            case 4: // one pair
                $onePair[$bid] = $cards;
                break;
            case 5: //high card
                $highCard[$bid] = $cards;
                break;
        }
    }
    asort($five);
    asort($four);
    asort($fullHouse);
    asort($three);
    asort($twoPair);
    asort($onePair);
    asort($highCard);

    $i = 1;
    $total = 0;
    $results = [$highCard, $onePair, $twoPair, $three, $fullHouse, $four, $five];
    foreach ($results as $result) {
        reset($result);

        foreach ($result as $bid => $hand) {
            $total += $bid * $i;
            $i++;
        }
    }

    return $total;
}

function day8()
{
    $input = getFileContent('08');
    $instructions = str_split(array_shift($input));
    $instructionsCount = count($instructions);
    array_shift($input);
    $matrix = [];
    $total = 0;
    $startNodes = [];
    foreach ($input as $row) {
        preg_match_all('/[A-Z0-9]+/', $row, $matches);
        $resultSet = reset($matches);
        $matrix[$resultSet[0]] = ['L' => $resultSet[1], 'R' => $resultSet[2]];
        if (preg_match('/[A-Z0-9]{2}A/', $resultSet[0], $match1)) {
            $startNodes[] = $resultSet[0];
        }
    }

//    $key = 'AAA';
    $i = 0;
    $steps = 0;
//    while($key != 'ZZZ') {
    $visitedEndNode = [];
    $visitedSteps = [];
    $startNodesAmount = count($startNodes);
    while (count($visitedEndNode) < $startNodesAmount) {
        $instruction = $instructions[$i];
        $steps++;
        foreach ($startNodes as &$startNode) {
            $startNode = $matrix[$startNode][$instruction];
            if (!(preg_match('/[?^Z]/', $startNode, $match) === 0)) {
                $node = [$startNode, $steps];
                if (!in_array($startNode, $visitedEndNode)) {
                    $visitedEndNode[] = $startNode;
                    $visitedSteps[] = $steps;
                }
            }
        }
//        $key = $matrix[$key][$instruction];
        $i = ($i + 1) % $instructionsCount;
        $total++;
    }
    $lcm = array_pop($visitedSteps);
    while ($number = array_pop($visitedSteps)) {
        $lcm = lcm($lcm, $number);
    }

    return $lcm;
//    return $total;
}

function lcm(int $number1, int $number2)
{
    $a = max($number1, $number2);
    $b = min($number1, $number2);
    while ($a !== 0) {
        $gcd = $a;
        $a = $b % $a;
        $b = $gcd;
    }
    return abs($number1 * $number2) / $gcd;
}

function day9()
{
    $input = getFileContent('09');
    $total = 0;
    foreach ($input as $row) {
        preg_match_all('/-?[0-9]+/', $row, $matches);
        $numbers = reset($matches);
        $currentRow = $numbers;
        $matrix = [];
        $matrix[] = $currentRow;
        while (1 < count(array_unique($currentRow))) {
            $currentRow = getNextRow($currentRow);
            $matrix[] = $currentRow;
        }
        $revMatrix = array_reverse($matrix);
        $history = current($revMatrix);
        while ($nextRow = next($revMatrix)) {
            $nextRowKey = key($revMatrix);
//            $revHistory = array_reverse($history);
            $revHistory = $history;
            $currentVal = $revHistory[0];
//            $rowBeforeVal = end($nextRow);
            $rowBeforeVal = reset($nextRow);

//            $revMatrix[$nextRowKey][] = $currentVal + $rowBeforeVal;
            array_unshift($revMatrix[$nextRowKey], $rowBeforeVal - $currentVal);
            $history = current($revMatrix);
        }
//        $total += end($history);
        $total += reset($history);
    }
    return $total;
}

function getNextRow(array $numbers): array
{
    $first = array_shift($numbers);
    $nextRow = [];
    foreach ($numbers as $number) {
        $nextRow[] = $number - $first;
        $first = $number;
    }

    return $nextRow;
}

function day10()
{
    $input = getFileContent('10');
    $startPoint = [];
    $rowKey = 0;
    foreach ($input as $rowKey => $row) {
        if (preg_match('/S/', $row, $match, PREG_OFFSET_CAPTURE)) {
            $startPoint = reset($match);
            break;
        }
    }
    $matrix = $input;
    $p = $startPoint[1];
    $prevCell = $input[$rowKey][$p - 1];
    $nextCell = $input[$rowKey][$p + 1];
    $aboveCell = $input[$rowKey - 1][$p];
    $downCell = $input[$rowKey + 1][$p];
    $neighbours = [];
    $leftSite = ['-' => 0, 'L' => 1, 'F' => -1];
    $rightSite = ['-' => 0, 'J' => 1, '7' => -1,];
    $upSite = ['|' => 0, 'F' => 1, '7' => -1];
    $downSite = ['|' => 0, 'L' => 1, 'J' => -1];
    $matrix[$rowKey][$p] = 'B';
    $possibleStart = [];
    if (key_exists($prevCell, $leftSite)) {
        $neighbours[] = ['y' => $rowKey, 'x' => $p - 1, 'letter' => $prevCell, 'prev' => ['x' => $p, 'y' => $rowKey]];
        $possibleStart = array_merge($possibleStart, array_keys($rightSite));
    }
    if (key_exists($nextCell, $rightSite)) {
        $neighbours[] = ['y' => $rowKey, 'x' => $p + 1, 'letter' => $nextCell, 'prev' => ['x' => $p, 'y' => $rowKey]];
        $possibleStart = array_merge($possibleStart, array_keys($leftSite));
    }
    if (key_exists($aboveCell, $upSite)) {
        $neighbours[] = ['y' => $rowKey - 1, 'x' => $p, 'letter' => $aboveCell, 'prev' => ['x' => $p, 'y' => $rowKey]];
        $possibleStart = array_merge($possibleStart, array_keys($downSite));
    }
    if (key_exists($downCell, $downSite)) {
        $neighbours[] = ['y' => $rowKey + 1, 'x' => $p, 'letter' => $downCell, 'prev' => ['x' => $p, 'y' => $rowKey]];
        $possibleStart = array_merge($possibleStart, array_keys($upSite));
    }

    $possibleStart = array_count_values($possibleStart);

    arsort($possibleStart);
    $start = key($possibleStart);
    $input[$rowKey][$p] = $start;
    $steps = 1;
    $neighboursCount = 0;
    while ($neighbour = array_shift($neighbours)) {
        $next = getNextCell($neighbour);
        $next['letter'] = $input[$next['y']][$next['x']];
        $current = reset($neighbours);
        $steps = $steps + $neighboursCount % 2;
        $matrix[$neighbour['y']][$neighbour['x']] = 'B';
        if ($current['x'] === $next['x'] && $current['y'] === $next['y']) {
            $matrix[$current['y']][$current['x']] = 'B';
            break;
        }
        $neighbours[] = $next;
        $neighboursCount++;
    }
    $i = 0;
    foreach ($matrix as $y => $row) {
        $cells = str_split($row);
        $outSide = true;
        $entryLetter = '';
        foreach ($cells as $x => $cell) {
            if ($cell === 'B') {
                $originCell = $input[$y][$x];
                if (in_array($originCell, ['F', 'L'])) {
                    $entryLetter = $originCell;
                } elseif (in_array($originCell, ['J', '7'])) {
                    if (
                        ($entryLetter === 'F' && $originCell === 'J')
                        || ($entryLetter === 'L' && $originCell == '7')
                    ) {
                        $outSide = !$outSide;
                    }
                } elseif ($originCell === '|') {
                    $outSide = !$outSide;
                }
            } else {
                if (!$outSide) {
                    $i++;
                }
            }
        }
    }

    return $i;
}

function getNextCell($currentCell): array
{
    $prevCell = $currentCell['prev'];
    $y = $currentCell['y'];
    $x = $currentCell['x'];
    $diffX = $currentCell['x'] - $prevCell['x'];
    $diffY = $currentCell['y'] - $prevCell['y'];
    switch ($currentCell['letter']) {
        case '-':
            $x = $currentCell['x'] + ($diffX);
            break;
        case 'L':
            $y = $currentCell['y'] - abs($diffX);
            $x = $currentCell['x'] + abs($diffY);
            break;
        case 'J':
            $y = $currentCell['y'] - abs($diffX);
            $x = $currentCell['x'] - abs($diffY);
            break;
        case '7':
            $y = $currentCell['y'] + abs($diffX);
            $x = $currentCell['x'] - abs($diffY);
            break;
        case 'F':
            $y = $currentCell['y'] + abs($diffX);
            $x = $currentCell['x'] + abs($diffY);
            break;
        case '|':
            $y = $currentCell['y'] + ($diffY);
            break;
    }

    return ['x' => $x, 'y' => $y, 'prev' => $currentCell];
}

function day11()
{
    $input = getFileContent('11');

    $planets = [];
    $result = 0;
    $matrix = $input;
    $matrixWidth = strlen($input[0]);
    $i = 0;
    $columns = [];
    $expansionSize = 999999;
    $rowsForExpand = [];
    $rowsWithPlanet = [];
    foreach ($input as $y => $row) {
        preg_match_all('/#/', $row, $matches, PREG_OFFSET_CAPTURE);
        $matches = reset($matches);
        if (empty($matches)) {
            $rowsForExpand[] = $y;
        } else {
            $rowsWithPlanet[] = $y;
        }

        for ($k = 0; $k < $matrixWidth; $k++) {
            $columns[$k] .= $row[$k];
        }
    }
    foreach ($columns as $x => $column) {
        preg_match_all('/#/', $column, $matches, PREG_OFFSET_CAPTURE);
        $matches = reset($matches);
        if (empty($matches)) {
            foreach ($matrix as &$row) {
                $row = substr_replace($row, str_repeat('.', $expansionSize), $x + $i, 0);
            }
            $i += $expansionSize;
        }
    }
    $diff = 0;
    foreach ($rowsWithPlanet as $space) {
        preg_match_all('/#/', $matrix[$space], $matches, PREG_OFFSET_CAPTURE);
        $matches = reset($matches);
        $newSpace = $space + $diff;
        if (!empty($rowsForExpand && $space >= min($rowsForExpand))) {
            $diff += $expansionSize;
            $newSpace += $expansionSize;
            array_shift($rowsForExpand);
        }
        foreach ($matches as $planet) {
            $planets[] = ['y' => $newSpace, 'x' => $planet[1]];
        }
    }

    while ($fPlanet = array_shift($planets)) {
        foreach ($planets as $planet) {
            $zwischenDis = calcManhattanDistance($fPlanet, $planet);
            $result += $zwischenDis;
        }
    }

    return $result;
}

function calcManhattanDistance(array $firstP, array $secondP)
{
    $y1 = max($secondP['y'], $firstP['y']);
    $y2 = min($secondP['y'], $firstP['y']);
    $x1 = max($secondP['x'], $firstP['x']);
    $x2 = min($secondP['x'], $firstP['x']);

    return ($y1 - $y2) + ($x1 - $x2);
}

function day12()
{
    $input = getFileContent('12');

    foreach ($input as $row) {
        preg_match_all('/[.\?#]+|\\d/', $row, $matches);
        $criteria = reset($matches);
        $springs = array_shift($criteria);
        $springsLength = strlen($springs);
        $criteriaCount = count($criteria);
        $criteriaSum = array_sum($criteria) + $criteriaCount - 1;
        if ($criteriaSum === $springsLength) {
            $sum++;
        } elseif ($springsLength > $criteriaSum) {
            $diff = $springsLength - $criteriaSum;
            $dotCount = substr_count($springs, '.');
            $groups = array_values(array_filter(explode('.', $springs)));
            if (count($groups) === $criteriaCount) {
                foreach ($groups as $key => $group) {
                    $groupLength = strlen($group);
                    if (strpos($group, '#') === false) {
                        $sum += $groupLength * $criteria[$key];
                    } else {
                        if ($groupLength === $criteriaCount) {
                            // dont add possibility
                        } else {
                            $hashCount = substr_count($group, '#');
                        }
                    }
                }
            }
        }
    }

    return "Only a try, no result";
}

function day13()
{
    $input = getFileContent('13');
    $group = [];
    $groups = [];
    foreach ($input as $row) {
        if ($row === '') {
            $groups[] = $group;
            $group = [];
        } else {
            $group[] = str_split($row);
        }
    }
    $groups[] = $group;
    $sum = 0;
    foreach ($groups as $group) {
        $columns = [];
        $rowSize = count($group[0]);
        for ($x = 0; $x < $rowSize; $x++) {
            $columns[] = array_column($group, $x);
        }
        $multiplication = 100;
        $flippedC = 0;
        foreach ([$group, $columns] as $pattern) {
            $patternSize = count($pattern);
            for ($y = 0; $y < $patternSize - 1; $y++) {
                $diffPattern = implode('', $pattern[$y]) & implode('', $pattern[$y + 1]);
                $diffCount = substr_count($diffPattern, '"');
                if ($diffCount <= 1) {
                    $flipped = (bool)$diffCount;
//                if ($pattern[$y] == $pattern[$y + 1]) {
                    for ($i = $y - 1, $k = $y + 2; $i >= 0 && $k < $patternSize; $i--, $k++) {
                        $diffNextPattern = implode('', $pattern[$i]) & implode('', $pattern[$k]);
                        $nextDiffCount = substr_count($diffNextPattern, '"');
                        $flippCount = $nextDiffCount + $diffCount;
                        if ($flippCount === 1) {
                            $flipped = true;
                        }

                        if ($flippCount > 1) {
//                        if ($pattern[$i] != $pattern[$k]) {
                            continue 2;
                        }
                    }
                    if (!$flipped) {
                        continue;
                    }
                    $sum += ($y + 1) * $multiplication;
                    continue 3;
                }
            }

            $multiplication = 1;
            $flippedC++;
        }
    }

    return $sum;
}

function day14()
{
    $input = getFileContent('14');

    $matrix = [];
    foreach ($input as $row) {
        $matrix[] = str_split($row);
    }
    $columnsCount = count($matrix[0]);

    $directions = [
        'north',
        'west',
        'south',
        'east'
    ];

    $rounds = 1000000000;
    $calculatedMatrix = [];
    $cycle = [];
    for ($k = 1; $k < $rounds; $k++) {
        foreach ($directions as $direction) {
            $columns = [];
            switch ($direction) {
                case 'north':
                    for ($i = 0; $i < $columnsCount; $i++) {
                        $columns[] = tilt(array_column($matrix, $i));
                    }
                    break;
                case 'west':
                    foreach ($matrix as &$row) {
                        $row = tilt($row);
                    }
                    break;
                case 'south':
                    for ($i = 0; $i < $columnsCount; $i++) {
                        $columns[] = array_reverse(tilt(array_reverse(array_column($matrix, $i))));
                    }
                    break;
                case 'east':
                    foreach ($matrix as &$row) {
                        $row = array_reverse(tilt(array_reverse($row)));
                    }
                    break;
            }

            foreach ($columns as $columnKey => $columnsCell) {
                foreach ($columnsCell as $rowKey => $cell) {
                    $matrix[$rowKey][$columnKey] = $cell;
                }
            }
        }
        $caMatrix[$k] = $matrix;
        $matrixHash = md5(serialize($matrix));
        if (array_key_exists($matrixHash, $cycle)) {
            $t = $k - count($cycle);
            $searchIndex = ($rounds - $t) % ($k - $t) + $t;
            break;
        }
        if (in_array($matrixHash, $calculatedMatrix)) {
            $cycle[$matrixHash] = $k;
        } else {
            $calculatedMatrix[$k] = $matrixHash;
        }
    }
    return calcWeight($caMatrix[$searchIndex]);
}

function tilt(array $input)
{
    $stringInput = implode('', $input);
    preg_match_all('/([O.]*)#?/', $stringInput, $matches, PREG_OFFSET_CAPTURE);
    $roundedRocks = array_pop($matches);
    $offset = 0;
    foreach ($roundedRocks as $roundRock) {
        $roundRocksCount = substr_count($roundRock[0], 'O');
        if ($roundRocksCount > 0) {
            $firstP = array_fill($offset, $roundRocksCount, 'O');
            $secondP = array_fill($offset + $roundRocksCount, strlen($roundRock[0]) - $roundRocksCount, '.');
            $newSub = $firstP + $secondP;
            $input = array_replace($input, $newSub);
        }
        $offset += strlen($roundRock[0]) + 1;
    }

    return $input;
}

function calcWeight(array $matrix)
{
    $revMatrix = array_reverse($matrix);
    $sum = 0;
    foreach ($revMatrix as $rowKey => $row) {
        $result = array_count_values($row);
        foreach ($result as $value => $count) {
            if ($value == 'O') {
                $sum += $count * ($rowKey + 1);
                continue 2;
            }
        }
    }

    return $sum;
}

function day15()
{
    $input = getFileContent('15');
    $rows = explode(',', reset($input));

    $total = 0;
    $container = [];
    foreach ($rows as $row) {
        preg_match('/[a-z]+/', $row, $boxMatches);
        preg_match('/-|=/', $row, $operatorMatches);
        preg_match('/\d/', $row, $lensMatches);
        $box = reset($boxMatches);
        $operator = reset($operatorMatches);
        $lens = reset($lensMatches);
        $boxIndex = getHash($box);
        if (isset($container[$boxIndex])) {
            $currentBox = $container[$boxIndex];
//            $boxesInside = array_column($currentBox, 'label');
            if (isset($currentBox[$box]) && $operator === '=') {
                $currentBox[$box]['lens'] = $lens;
            } elseif ($operator === '=') {
                $sortColumn = array_column($currentBox, 'sort');
                $sort = max($sortColumn) + 1;
                $currentBox[$box] = ['label' => $box, 'lens' => $lens, 'sort' => $sort];
            } elseif (isset($currentBox[$box])) {
                unset($currentBox[$box]);
            }
            $container[$boxIndex] = $currentBox;
        } elseif ($operator === '=') {
            $container[$boxIndex][$box] = ['label' => $box, 'lens' => $lens, 'sort' => 1];
        }
    }

    foreach ($container as $boxNumber => $boxes) {
        $multiplier = $boxNumber + 1;
        $sortedBox = array_values(array_column($boxes, 'lens', 'sort'));

        foreach ($sortedBox as $sortNumber => $lens) {
            $total += $multiplier * ($sortNumber + 1) * $lens;
        }
    }

    return $total;
}

function getHash(string $string)
{
    $multiplier = 17;
    $divisor = 256;
    $currentValue = 0;
    $chars = str_split($string);
    foreach ($chars as $char) {
        $currentValue += ord($char);
        $currentValue *= $multiplier;
        $currentValue %= $divisor;
    }

    return $currentValue;
}

//echo day1();
//echo day2();
//echo day3();
//echo day4();
//echo day5();
//echo day6();
//echo day7();
//echo day8();
//echo day9();
//echo day10();
//echo day11();
//echo day12();
//echo day13();
//echo day14();
echo day15();