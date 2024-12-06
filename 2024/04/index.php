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
    $xmasFound = 0;
    $matrix = [];
    foreach ($input as $line) {
        $matrix[] = str_split($line);
    }
    $xLength = count($matrix[0]);
    $yLength = count($matrix);
    for ($y = 0; $y < $yLength; $y++) {
        for ($x = 0; $x < $xLength; $x++) {
            if ($matrix[$y][$x] === 'X') {
                //check for diagonal left up
                if (isset($matrix[$y - 3][$x - 3]) && $matrix[$y - 3][$x - 3] === 'S') {
                    if ($matrix[$y - 1][$x - 1] === 'M' && $matrix[$y - 2][$x - 2] === 'A') {
                        $xmasFound++;
                    }
                }
                //check for up
                if (isset($matrix[$y - 3][$x]) && $matrix[$y - 3][$x] === 'S') {
                    if ($matrix[$y - 1][$x] === 'M' && $matrix[$y - 2][$x] === 'A') {
                        $xmasFound++;
                    }
                }
                //check diagonal right up
                if (isset($matrix[$y - 3][$x + 3]) && $matrix[$y - 3][$x + 3] === 'S') {
                    if ($matrix[$y - 1][$x + 1] === 'M' && $matrix[$y - 2][$x + 2] === 'A') {
                        $xmasFound++;
                    }
                }
                //check for right
                if (isset($matrix[$y][$x + 3]) && $matrix[$y][$x + 3] === 'S') {
                    if ($matrix[$y][$x + 1] === 'M' && $matrix[$y][$x + 2] === 'A') {
                        $xmasFound++;
                    }
                }
                //check for diagonal right down
                if (isset($matrix[$y + 3][$x + 3]) && $matrix[$y + 3][$x + 3] === 'S') {
                    if ($matrix[$y + 1][$x + 1] === 'M' && $matrix[$y + 2][$x + 2] === 'A') {
                        $xmasFound++;
                    }
                }
                //check for down
                if (isset($matrix[$y + 3][$x]) && $matrix[$y + 3][$x] === 'S') {
                    if ($matrix[$y + 1][$x] === 'M' && $matrix[$y + 2][$x] === 'A') {
                        $xmasFound++;
                    }
                }
                //check for diagonal left down
                if (isset($matrix[$y + 3][$x - 3]) && $matrix[$y + 3][$x - 3] === 'S') {
                    if ($matrix[$y + 1][$x - 1] === 'M' && $matrix[$y + 2][$x - 2] === 'A') {
                        $xmasFound++;
                    }
                }
                //chech for left
                if (isset($matrix[$y][$x - 3]) && $matrix[$y][$x - 3] === 'S') {
                    if ($matrix[$y][$x - 1] === 'M' && $matrix[$y][$x - 2] === 'A') {
                        $xmasFound++;
                    }
                }
            }
        }
    }

    echo $xmasFound;
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
    $xMasFound = 0;
    $matrix = [];
    foreach ($input as $line) {
        $matrix[] = str_split($line);
    }
    $xLength = count($matrix[0]);
    $yLength = count($matrix);
    for ($y = 0; $y < $yLength; $y++) {
        for ($x = 0; $x < $xLength; $x++) {
            if ($matrix[$y][$x] === 'A') {
                //check for diagonal left up
                if (isset($matrix[$y - 1][$x - 1]) && $matrix[$y - 1][$x - 1] === 'M'
                    && isset($matrix[$y + 1][$x - 1]) && $matrix[$y + 1][$x - 1] === 'M'
                    && isset($matrix[$y - 1][$x + 1]) && $matrix[$y - 1][$x + 1] === 'S'
                    && isset($matrix[$y + 1][$x + 1]) && $matrix[$y + 1][$x + 1] === 'S'
                ) {
                    $xMasFound++;
                }

                if (isset($matrix[$y - 1][$x - 1]) && $matrix[$y - 1][$x - 1] === 'S'
                    && isset($matrix[$y + 1][$x - 1]) && $matrix[$y + 1][$x - 1] === 'S'
                    && isset($matrix[$y - 1][$x + 1]) && $matrix[$y - 1][$x + 1] === 'M'
                    && isset($matrix[$y + 1][$x + 1]) && $matrix[$y + 1][$x + 1] === 'M'
                ) {
                    $xMasFound++;
                }

                if (isset($matrix[$y - 1][$x - 1]) && $matrix[$y - 1][$x - 1] === 'M'
                    && isset($matrix[$y + 1][$x - 1]) && $matrix[$y + 1][$x - 1] === 'S'
                    && isset($matrix[$y - 1][$x + 1]) && $matrix[$y - 1][$x + 1] === 'M'
                    && isset($matrix[$y + 1][$x + 1]) && $matrix[$y + 1][$x + 1] === 'S'
                ) {
                    $xMasFound++;
                }

                if (isset($matrix[$y - 1][$x - 1]) && $matrix[$y - 1][$x - 1] === 'S'
                    && isset($matrix[$y + 1][$x - 1]) && $matrix[$y + 1][$x - 1] === 'M'
                    && isset($matrix[$y - 1][$x + 1]) && $matrix[$y - 1][$x + 1] === 'S'
                    && isset($matrix[$y + 1][$x + 1]) && $matrix[$y + 1][$x + 1] === 'M'
                ) {
                    $xMasFound++;
                }
            }
        }
    }

    echo $xMasFound;
}