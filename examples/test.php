<?php

include __DIR__ . '/../vendor/autoload.php';

$callStartTime = microtime(true);

// $differ = new CsvDiff\Csv\Differ(__DIR__ . '/data/500000 Sales Records.csv', __DIR__ . '/data/500000 Sales Records.csv', [6]);

$differ = new CsvDiff\Csv\Differ(__DIR__ . '/data/small_1.csv', __DIR__ . '/data/small_2.csv', [3, 1]);
$differ->ignoreColumns([7, 8, 12]);
$differ->compare();

$callEndTime = microtime(true);
$loadCallTime = $callEndTime - $callStartTime;

echo PHP_EOL;
echo 'Call time to compare the files was ' , sprintf('%.4f', $loadCallTime) , ' seconds' , PHP_EOL;

// Echo memory usage
echo ' Current memory usage: ' , (memory_get_usage(true) / 1024) , ' KB' , PHP_EOL;
echo '    Peak memory usage: ' , (memory_get_peak_usage(true) / 1024) , ' KB' , PHP_EOL;
