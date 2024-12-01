<?php
function getFileContent(string $file): array
{
    return explode("\r\n", file_get_contents($file . '.txt'));
}
