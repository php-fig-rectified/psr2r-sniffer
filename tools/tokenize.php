<?php
require_once 'vendor/autoload.php';

$file = !empty($argv[1]) ? $argv[1] : null;
if (!file_exists($file)) {
    throw new \Exception('Please provide a valid file.');
}

$verbose = !empty($argv[2]) && in_array($argv[2], ['--verbose', '-v']);

var_dump($verbose);

$tokenizer = new \PSR2R\Tools\Tokenizer();

$tokenizer->tokenize($file, $verbose);
