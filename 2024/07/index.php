<?php

require_once('../inputReader.php');

if (isset($_GET['part']) && $_GET['part'] == '2') {
    part02();
} else {
    part01();
}

function part01()
{
    $input2 = getFileContent('input');
    $example = getFileContent('example');
    $input = $input2;
    $operators = ['+', '*'];
    $permutations = generatePermutations($operators, 12);
    $correctEquation = 0;
    $incorrectEquations = [];
    $maxCount = 0;
    foreach ($input2 as $lineNumber => $line) {
        [$result, $params] = explode(':', $line);
        preg_match_all('/\d+/', $params, $paramMatches);
        $parameter = reset($paramMatches);
        foreach ($permutations as $permutation) {
            $tmpResult = $parameter[0];

            $positions = count($parameter);
            $maxCount = max($maxCount, $positions);
            for ($i = 0; $i < $positions - 1 ; $i++) {
                switch ($permutation[$i]) {
                    case '+':
                        $tmpResult += $parameter[$i + 1];
                        break;
                    case '*':
                        $tmpResult *= $parameter[$i + 1];
                        break;
                }
            }
            if ($tmpResult == $result) {
                $correctEquation += intval($result);
                unset($input[$lineNumber]);
                continue 2;
            }
        }
    }

    $correctEquation2 = 0;
    $operators = ['+', '*', '||'];
    $permutations = generatePermutations($operators, $maxCount);
    foreach ($input as $line) {
        [$result, $params] = explode(':', $line);
        preg_match_all('/\d+/', $params, $paramMatches);
        $parameter = reset($paramMatches);

        foreach ($permutations as $permutation) {
            $tmpResult = $parameter[0];
            $positions = count($parameter) - 1;
            for ($i = 0; $i < $positions; $i++) {
                switch ($permutation[$i]) {
                    case '+':
                        $tmpResult += $parameter[$i + 1];
                        break;
                    case '*':
                        $tmpResult *= $parameter[$i + 1];
                        break;
                    case '||':
                        $tmpResult = intval($tmpResult . $parameter[$i + 1]);
                }
            }
            if ($tmpResult == $result) {
                $correctEquation2 += intval($result);
                continue 2;
            }
        }
    }

    echo $correctEquation + $correctEquation2;
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
}

function generatePermutations($inputArray, $positions)
{
    $result = [];
    $count = count($inputArray);

    // Total number of permutations
    $totalPermutations = pow($count, $positions);

    // Generate permutations using two loops
    for ($i = 0; $i < $totalPermutations; $i++) {
        $currentPermutation = [];
        $temp = $i;

        // Build the current permutation
        for ($j = 0; $j < $positions; $j++) {
            $index = $temp % $count; // Get the index for the current position
            $currentPermutation[] = $inputArray[$index]; // Append the corresponding element
            $temp = (int)($temp / $count); // Move to the next position
        }

        $result[] = $currentPermutation; // Add the current permutation to the result
    }

    return $result;
}