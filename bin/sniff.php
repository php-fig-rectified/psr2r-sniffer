#!/usr/bin/php -q
<?php

/**
 * Bin stub.
 */
$options = [
	__DIR__ . '/../../../autoload.php',
	__DIR__ . '/../../autoload.php',
	__DIR__ . '/../vendor/autoload.php',
	__DIR__ . '/vendor/autoload.php'
];
foreach ($options as $file) {
	if (file_exists($file)) {
		define('SNIFFER_COMPOSER_INSTALL', $file);
		break;
	}
}
require SNIFFER_COMPOSER_INSTALL;

$sniffer = new \PSR2R\Tools\Sniffer($argv);

$sniffer->sniff();
echo 0;
