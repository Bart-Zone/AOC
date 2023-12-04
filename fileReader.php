<?php

function getFileContent(string $day){
    return explode("\r\n", file_get_contents('inputFiles/' .  $day . '/input.txt'));
}