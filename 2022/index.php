<?php
require_once('../fileReader.php');

function day1()
{
    $input = getFileContent('01');
    $i = 0;
    $total = 0;
    $elf = [];
    foreach ($input as $calorie) {
        if ($calorie === '' ) {
            $elf[] = $total;
            $total = 0;
            $i++;
        } else {
            $total += (int)$calorie;
        }
    }
    $elf[] = $total;

    rsort($elf);
    echo "Part 1: ";
    echo $elf[0];
    echo "<br>";
    echo "Part 2: ";
    echo array_sum(array_slice($elf, 0, 3));
}

function day2()
{
    $input = getFileContent('02');
    // A, X Rock
    // B, Y Paper
    // C, Z Scissors
    $choosingPoints = [
        'X' => 1,
        'Y' => 2,
        'Z' => 3
    ];

    $winner = [
        'A' => 'Z',
        'B' => 'X',
        'C' => 'Y',
        'X' => 'C',
        'Y' => 'A',
        'Z' => 'B'
    ];

    $draw = [
        'A' => 'X',
        'B' => 'Y',
        'C' => 'Z',
        'X' => 'A',
        'Y' => 'B',
        'Z' => 'C'
    ];

    $todo = [
        'X' => array_flip($winner), // need to loos, looser ist the opposite of winner
        'Y' => $draw, // need to draw
        'Z' => $winner // need to win
    ];

    $winnerPoints = [
        'Z' => 6,
        'Y' => 3,
        'X' => 0
    ];

    $part1Points = 0;
    $parts2Points = 0;

    foreach ($input as $round) {
        $firstP = substr($round, 0, 1);
        $secondP = substr($round, 2, 1);

        //part 1
        $part1Points += $choosingPoints[$secondP];
        if ($winner[$secondP] == $firstP) {
            $part1Points += 6;
        } elseif ($draw[$secondP] == $firstP) {
            $part1Points += 3;
        }

        //part 2
        $result = array_flip($todo[$secondP]);
        $secondPlayer = $result[$firstP];
        $parts2Points += $choosingPoints[$secondPlayer];
        $parts2Points += $winnerPoints[$secondP];
    }

    echo "Part 1: ";
    echo $part1Points;
    echo "<br>";
    echo "Part 2 : ";
    echo $parts2Points;
}

function day3()
{
    $inputs = getFileContent('03');
    $lowerChars = range('a', 'z');
    $upperChars = range('A', 'Z');
    $priority = array_combine(array_merge($lowerChars, $upperChars), range(1, 52));
    $total = 0;
    foreach ($inputs as $rucksackContent) {
        $length = strlen($rucksackContent);
        $part1 = substr($rucksackContent, 0, floor($length / 2));
        $part2 = substr($rucksackContent, floor($length / 2));
        $part1Arr = str_split($part1, 1);
        $part2Arr = str_split($part2, 1);
        $intersect = array_intersect($part1Arr, $part2Arr);
        $total += $priority[current($intersect)];
    }
    $part2Total = 0;
    for ($i = 0; $i < count($inputs); $i = $i + 3) {
        $part1 = str_split($inputs[$i]);
        $part2 = str_split($inputs[$i + 1]);
        $part3 = str_split($inputs[$i + 2]);
        $intersect = array_intersect($part1, $part2, $part3);
        $part2Total += $priority[current($intersect)];
    }

    echo "Part 1: $total";
    echo "<br>";
    echo "Part 2: $part2Total";
}

function day4()
{
    $input = getFileContent('04');
    $total = 0;
    $total2 = 0;
    foreach ($input as $row) {
        $pairs = explode(',', $row);
        $sections = [];
        foreach ($pairs as $pair) {
            $minMax = explode('-', $pair);
            $sections[] = range($minMax[0], $minMax[1]);
        }
        $insersect = array_intersect($sections[0], $sections[1]);
        $intersectionCount = count($insersect);
        if ($intersectionCount === count($sections[0]) || $intersectionCount === count($sections[1])) {
            $total++;
        }
        if ($intersectionCount > 0) {
            $total2++;
        }

    }

    echo "Part 1: $total";
    echo "<br>";
    echo "Part 2: $total2";
}

function day5()
{
    $input = getFileContent('05');
    $length = strlen($input[0]);
    $bucketsQuantity = ceil($length / 4);
    $buckets = [];
    for ($i = 1; $i <= $bucketsQuantity; $i++) {
        $buckets[$i] = [];
    }
    $bucketsElements = 0;
    $commands = [];
    foreach ($input as $startState) {
        $j = 0;
        foreach ($buckets as $bucketNumber => $bucket) {
            $crate = trim(substr($startState, $j * 4, 4));
            if ($crate !== '') {
                $crateName = substr($crate, 1, 1);
                $buckets[$bucketNumber] = [$crateName, ...$bucket];
            }
            $j++;
        }
        $bucketsElements++;
        if (str_contains($startState, '1')) {
            $commands = array_slice($input, $bucketsElements + 1);
            break;
        }

    }
    $part1Buckets = $buckets;
    $part2Buckets = $buckets;
    foreach ($commands as $command) {
        $allParts = explode(' ', $command);
        $amount = (int)$allParts[1];
        $from = (int)$allParts[3];
        $to = (int)$allParts[5];
        $elements = array_slice($part2Buckets[$from], -$amount);
        for ($i = 0; $i < $amount; $i++) {
            $part1Buckets[$to][] = array_pop($part1Buckets[$from]);
            array_pop($part2Buckets[$from]);
        }
        $part2Buckets[$to] = array_merge($part2Buckets[$to], $elements);
    }
    echo "Part 1:";
    foreach ($part1Buckets as $crate) {
        echo end($crate);
    }
    echo "<br>";
    echo "Part 2:";
    foreach ($part2Buckets as $crate) {
        echo end($crate);
    }
}

function day6()
{
    $inputs = getFileContent('06');
    $input = array_pop($inputs);

    $uniqueLengths = [4, 14];
    foreach ($uniqueLengths as $part => $uniqueLength) {
        $usedChars = [];
        $stream = str_split($input, 1);
        for ($i = 0; $i < $uniqueLength; $i++) {
            $usedChars[] = array_shift($stream);
        }
        $j = $uniqueLength;
        foreach ($stream as $char) {
            $uniqueUsed = array_unique($usedChars);
            if (count($uniqueUsed) === $uniqueLength) {
                break;
            }
            array_shift($usedChars);
            $usedChars[] = $char;
            $j++;
        }
        echo "Part " . $part + 1 . ": $j";
        echo "<br>";
    }
}

echo "Day 1 <br>";
day1();
echo "<br><br>";
echo "Day 2 <br>";
day2();
echo "<br><br>";
echo "Day 3 <br>";
day3();
echo "<br><br>";
echo "Day 4 <br>";
day4();
echo "<br><br>";
echo "Day 5 <br>";
day5();
echo "<br><br>";
echo "Day 6 <br>";
day6();
echo "<br>";
echo "Day 7 <br>";