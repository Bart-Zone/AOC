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

    $operators = ['+', '*', '||'];
    $permutations = generatePermutations($operators, 12);
    $correctEquation = 0;
    foreach ($input as $line) {
        [$result, $params] = explode(':', $line);
        preg_match_all('/\d+/', $params, $paramMatches);
        $parameter = reset($paramMatches);

        foreach ($permutations as $permuation) {
            $permResult = 0;
            $tmpResult = $parameter[0];
            $positions = count($parameter);
            for($i = 0; $i<$positions; $i++) {
                switch ($permuation[$i]) {
                    case '+':
                        $tmpResult += $parameter[$i + 1];
                        break;
                    case '*':
                        $tmpResult *= $parameter[$i + 1];
                        break;
                    case '|':
                        $tmpResult = intval($tmpResult . $parameter[$i + 1]);
                }
                $permResult += $tmpResult;
            }
            if ($tmpResult == $result) {
                $correctEquation += $result;
                continue 2;
            }
        }
    }

    echo $correctEquation;
}

function part02()
{
    $input = getFileContent('input');
    $example = getFileContent('example');
}

function generatePermutations($inputArray, $positions) {
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