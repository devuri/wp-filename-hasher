<?php

return [
    'outputFile' => dirname(__DIR__) . '/filename-hasher.zip',
    'baseDir' => dirname(__DIR__),
    'files' => [
        'src',
        'vendor',
        'filename-hasher.php',
        'LICENSE',
    ],
    'exclude' => [],
];
