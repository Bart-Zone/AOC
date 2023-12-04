<?php
require_once('../fileReader.php');

function calcFuel($mass, $total, $recursive)
{
    $result = floor($mass / 3) - 2;
    if (!$recursive) {
        return $result;
    }
    if ($result <= 0) {
        return $total;
    }
    return calcFuel($result, $total + $result, true);
}

function day1_1()
{
    $input = getFileContent('01');
    $totalFuel = 0;
    foreach ($input as $mass) {
        $totalFuel += calcFuel($mass, 0, false);
    }

    echo $totalFuel;
}

function day1_2()
{
    $input = getFileContent('01');
    $totalFuel = 0;
    foreach ($input as $mass) {
        $totalFuel += calcFuel($mass, 0, true);
    }

    echo $totalFuel;

}

function runOppCode($newInput, $noun, $verb)
{
    $add = 1;
    $multiply = 2;
    $end = 99;

    $newInput[1] = $noun;
    $newInput[2] = $verb;
    for ($i = 0; $i < count($newInput); $i = $i + 4) {
        $firstI = $newInput[$i + 1];
        $secondI = $newInput[$i + 2];
        $resultI = $newInput[$i + 3];

        if ($newInput[$i] == $add) {
            $result = $newInput[$firstI] + $newInput[$secondI];
        } elseif ($newInput[$i] == $multiply) {
            $result = $newInput[$firstI] * $newInput[$secondI];
        } elseif ($newInput[$i] == $end) {
            break;
        }
        $newInput[$resultI] = $result;
    }
    return $newInput[0];
}

function day2_1()
{
    $input = getFileContent('02');
    $newInput = explode(',', $input[0]);

    echo runOppCode($newInput, 12, 2);
}

function day2_2()
{
    $input = getFileContent('02');
    $originalInput = explode(',', $input[0]);

    for ($noun = 0; $noun <= 99; $noun++) {
        for ($verb = 0; $verb <= 99; $verb++) {
            if (runOppCode($originalInput, $noun, $verb) == 19690720) {
                echo $noun;
                echo $verb;
                break;
            }
        }
    }

}

function day3_1()
{
    $input = getFileContent('03');
    $input = [
        'R75,D30,R83,U83,L12,D49,R71,U7,L72',
        'U62,R66,U55,R34,D71,R55,D58,R83'
    ];
    $coordinates = [];
    foreach ($input as $wirePaths) {
        $paths = explode(',', $wirePaths);
        $coordinates[] = goPath($paths);
    }

    $firstWire = $coordinates[0];
    $secondWire = $coordinates[1];
    $intersect = array_merge_recursive($firstWire, $secondWire);
    foreach ($intersect as $xCord => $yValues) {
        $test = array_count_values($yValues);
        if (in_array(2, $test)) {
            $yCord = array_search(2, $test);
            $distances[$xCord . '|y' . $yCord] = abs((int)substr($xCord, 1)) + abs($yCord);
        }
    }
    asort($distances);
    unset($distances['x0|y0']);
    echo current($distances);

    $newCords = explode('|', array_key_first($distances));
    $newX = substr($newCords[0], 1);
    $newY = substr($newCords[1], 1);
    foreach ($input as $wirePaths) {
        $paths = explode(',', $wirePaths);
        $steps[] = goPath($paths, $newX, $newY, true);
    }
    echo "<br>";
    echo "Part 2 : ";
    echo $steps[0][0] + $steps[1][0];

}

function goPath($wirePaths, $destinationX = 0, $destinationY = 0, $getSteps = false)
{
    $stepsGone = 0;
    $x = 0;
    $y = 0;
    $coordinates = [];
    foreach ($wirePaths as $path) {
        $dir = substr($path, 0, 1);
        $steps = (int)substr($path, 1);
        if ($dir == 'R') {
            for ($i = $x; $i <= $x + $steps; $i++) {
                $coordinates['x' . $i][] = $y;
                $coordinates['x' . $i] = array_unique($coordinates['x' . $i]);
                $stepsGone++;
                if ($getSteps && $i == $destinationX && $y == $destinationY)
                    return [$stepsGone];
            }
            $x += $steps;
        } elseif ($dir == 'L') {
            for ($i = $x; $i >= $x - $steps; $i--) {
                $coordinates['x' . $i][] = $y;
                $coordinates['x' . $i] = array_unique($coordinates['x' . $i]);
                $stepsGone++;
                if ($getSteps && $i == $destinationX && $y == $destinationY)
                    return [$stepsGone];
            }
            $x -= $steps;
        } elseif ($dir == 'U') {
            for ($i = $y; $i <= $y + $steps; $i++) {
                $coordinates['x' . $x][] = $i;
                $stepsGone++;
                if ($getSteps && $x == $destinationX && $i == $destinationY)
                    return [$stepsGone];
            }
            $coordinates['x' . $x] = array_unique($coordinates['x' . $x]);
            $y += $steps;
        } elseif ($dir == 'D') {
            for ($i = $y; $i >= $y - $steps; $i--) {
                $coordinates['x' . $x][] = $i;
                $stepsGone++;
                if ($getSteps && $x == $destinationX && $i == $destinationY)
                    $stepsGone--;
                    return [$stepsGone];
            }
            $coordinates['x' . $x] = array_unique($coordinates['x' . $x]);
            $y -= $steps;
        }
        $stepsGone--;
        $coordinates['x' . $x] = array_unique($coordinates['x' . $x]);
    }

    return $coordinates;
}

//day1_1();
//day1_2();
//day2_1();
//day2_2();
day3_1();