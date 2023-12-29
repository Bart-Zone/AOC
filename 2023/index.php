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

//echo day1();
//echo day2();
//echo day3();
//echo day4();
//echo day5();
//echo day6();
echo day7();
