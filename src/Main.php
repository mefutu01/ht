<?php

use M3futu\Ht\AnalyzerService;
use M3futu\Ht\JsonParser;
use PHPUnit\Runner\FileDoesNotExistException;
require __DIR__ . '/../vendor/autoload.php';

if ($argc <= 1)
    throw new InvalidArgumentException("Необходимо передать путь к файлу json");

$file = $argv[1];

if (!file_exists($file)) {
    throw new FileDoesNotExistException("$file не существует");
}

$parser = new JsonParser();
$records = $parser->parse($file);

$analyzer = new AnalyzerService;
$analyzer->sort($records);
$analyzer->print();