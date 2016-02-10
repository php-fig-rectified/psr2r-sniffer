#!/usr/bin/php -q
<?php

$minVersion = '5.4.16';
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'));
    if (isset($composer->require->php)) {
        $minVersion = preg_replace('/([^0-9\.])/', '', $composer->require->php);
    }
}
if (version_compare(phpversion(), $minVersion, '<')) {
    fwrite(STDERR, sprintf("Minimum PHP version: %s. You are using: %s.\n", $minVersion, phpversion()));
    exit(-1);
}


require_once dirname(__DIR__) . '/vendor/autoload.php';

$tokenizer = new \PSR2R\Tools\Tokenizer($argv);

$tokenizer->tokenize();
echo 0;
