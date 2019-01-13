<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Seld\JsonLint\JsonParser;

$iterator = new DirectoryIterator(__DIR__ . '/../storage/app/public/dependencies');

$failures = [];
echo "Parsing dependency JSON files" . PHP_EOL . PHP_EOL;
$jsonParser = new JsonParser();
foreach ($iterator as $item) {
    if ($item->isDot()) {
        continue;
    }

    try {
        $jsonParser->parse(file_get_contents($item->getPathname()), JsonParser::DETECT_KEY_CONFLICTS);
        echo $item->getFilename() . '... Ok.' . PHP_EOL;
    } catch (Exception $exception) {
        echo $item->getFilename() . '... Fail!' . PHP_EOL;
        $failures[$item->getFilename()] = $exception->getMessage();
    }
}

if (count($failures) > 0) {

    echo PHP_EOL . '-------------------------------------------------' . PHP_EOL . PHP_EOL;

    echo 'Failures: ' . PHP_EOL . PHP_EOL;
    foreach ($failures as $failure => $reason) {
        echo $failure . ': ' . $reason . PHP_EOL . PHP_EOL;
    }

    exit(1);
}

exit(0);
