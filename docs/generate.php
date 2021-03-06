#!/usr/bin/env php
<?php
$phar = file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'composer.phar');
$command = 'vendor/bin/phpcs -e --standard=PSR2R/ruleset.xml';

exec($command, $output, $ret);

if ($ret !== 0) {
	exit('Invalid execution. Run from ROOT after composer install etc as `composer docs`.');
}

/** @noinspection ForeachSourceInspection */
foreach ($output as &$row) {
	$row = str_replace('  ', '- ', $row);
}
unset($row);

$content = implode(PHP_EOL, $output);

$content = <<<TEXT
# PSR2R Code Sniffer
$content

TEXT;

$file = __DIR__ . DIRECTORY_SEPARATOR . 'sniffs.md';

file_put_contents($file, $content);
exit($ret);
