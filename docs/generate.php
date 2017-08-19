#!/usr/bin/env php
<?php

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;
exec($root . 'vendor/bin/phpcs --standard=PSR2R/ruleset.xml -e', $output, $ret);
if ($ret !== 0) {
	exit('Invalid execution. Run from ROOT after composer install etc as `php docs/generate.php`.');
}

/** @noinspection ForeachSourceInspection */
foreach ($output as &$row) {
	$row = str_replace('  ', '- ', $row);
}
unset($row);

$content = implode(PHP_EOL, $output);

$content = <<<TEXT
# PSR2R Code Sniffer

$content;
TEXT;

$file = __DIR__ . DIRECTORY_SEPARATOR . 'sniffs.md';

file_put_contents($file, $content);
exit($ret);
