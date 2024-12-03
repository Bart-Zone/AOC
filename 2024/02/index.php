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
    $safeResult = 0;
    foreach ($input as $report) {
        $levels = explode(' ', $report);
        $originLevel = $levels;
        $tryLevels = [$levels];
        while ($levels = array_pop($tryLevels)) {
            $previousLevel = array_shift($levels);
            $previousDiff = 0;
            $retry = true;
            foreach ($levels as $key => $level) {
                $diff = (int)$previousLevel - (int)$level;
                $increase = $diff * $previousDiff;
                $previousDiff = $diff;
                $absDiff = abs($diff);
                $previousLevel = $level;
                if ($absDiff < 1 || $absDiff > 3 || $increase < 0 && !$re  try) {
                    $firstPossibility = $originLevel;
                    $secondPossibility = $originLevel;
                    unset($firstPossibility[$key]);
                    unset($secondPossibility[$key + 1]);
                    reset($firstPossibility);
                    reset($secondPossibility);
                    $tryLevels[] = $firstPossibility;
                    $tryLevels[] = $secondPossibility;
                    $retry = true;
                    continue 2;
                } elseif ($retry) {
                    continue 3;
                }
            }

        }
        $safeResult++;
    }

    echo $safeResult;
}