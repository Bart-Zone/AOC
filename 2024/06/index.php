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
    $matrix = [];
    $guardPosition = [];

    foreach ($input as $yCoordinate => $line) {
        $matrix[] = str_split($line);
        if (false !== $xCoordinate = strpos($line, '^')) {
            $guardPosition = [$yCoordinate, $xCoordinate];
        }
    }
    $y = $guardPosition[0];
    $x = $guardPosition[1];
    $directionsMap = [
        'left' => 'up',
        'up' => 'right',
        'right' => 'down',
        'down' => 'left'
    ];
    $dir = 'up';
    while (isset($matrix[$y][$x])) {
        if ($matrix[$y][$x] === '#') {
            switch ($dir) {
                case 'right':
                    $x--;
                    break;
                case 'left':
                    $x++;
                    break;
                case 'up';
                    $y++;
                    break;
                case 'down':
                    $y--;
                    break;
            }
            $dir = $directionsMap[$dir];
        }
        $matrix[$y][$x] = 'X';

        switch ($dir) {
            case 'right':
                $x++;
                break;
            case 'left':
                $x--;
                break;
            case 'up';
                $y--;
                break;
            case 'down':
                $y++;
                break;
        }
    }

    $result = 0;
    foreach ($matrix as $y => $line) {
        foreach ($line as $x => $cell) {
            if ($cell === 'X') {
                $result++;
            }
        }
    }

    echo $result;
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
    $matrix = [];
    $guardPosition = [];

    foreach ($input as $yCoordinate => $line) {
        $matrix[] = str_split($line);
        if (false !== $xCoordinate = strpos($line, '^')) {
            $guardPosition = [$yCoordinate, $xCoordinate];
        }
    }
    $y = $guardPosition[0];
    $x = $guardPosition[1];
    $directionsMap = [
        'left' => 'up',
        'up' => 'right',
        'right' => 'down',
        'down' => 'left'
    ];
    $dir = 'up';
    $possiblePoints = [];

    while (isset($matrix[$y][$x])) {
        if ($matrix[$y][$x] === '#') {
            switch ($dir) {
                case 'right':
                    $x--;
                    break;
                case 'left':
                    $x++;
                    break;
                case 'up';
                    $y++;
                    break;
                case 'down':
                    $y--;
                    break;
            }
            $dir = $directionsMap[$dir];
        } else {
            if ($guardPosition !== [$y, $x]) {
                $point = ['y' => $y, 'x' => $x];
                if (!in_array($point, $possiblePoints)) {
                    $possiblePoints[] = $point;
                }

            }
        }

        switch ($dir) {
            case 'right':
                $x++;
                break;
            case 'left':
                $x--;
                break;
            case 'up';
                $y--;
                break;
            case 'down':
                $y++;
                break;
        }
    }
    $loopDetected = false;
    $result = 0;
    foreach ($possiblePoints as $possiblePoint) {
        $coMatrix = $matrix;
        $coMatrix[$possiblePoint['y']][$possiblePoint['x']] = '#';
        $visitedPoints = [];
        $y = $guardPosition[0];
        $x = $guardPosition[1];
        $dir = 'up';
        while (isset($coMatrix[$y][$x])) {
            if ($coMatrix[$y][$x] === '#') {
                switch ($dir) {
                    case 'right':
                        $x--;
                        break;
                    case 'left':
                        $x++;
                        break;
                    case 'up';
                        $y++;
                        break;
                    case 'down':
                        $y--;
                        break;
                }
                if (in_array(implode('', [$y, $x, $dir]), $visitedPoints)) {
                    $result++;
                    $anchorPoint[] = [$possiblePoint['y'], $possiblePoint['x'], $dir];
                    continue 2;
                } else
                {
                    $visitedPoints[] = implode('', [$y, $x, $dir]);
                }

                $dir = $directionsMap[$dir];
            }

            switch ($dir) {
                case 'right':
                    $x++;
                    break;
                case 'left':
                    $x--;
                    break;
                case 'up';
                    $y--;
                    break;
                case 'down':
                    $y++;
                    break;
            }
        }
    }
    echo $result;
}