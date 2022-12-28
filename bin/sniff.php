#!/usr/bin/php -q
<?php

use PSR2R\Tools\Sniffer;

$options = [
	__DIR__ . '/../vendor/autoload.php',
	__DIR__ . '/vendor/autoload.php',
];
if (!empty($_SERVER['PWD'])) {
	array_unshift($options, $_SERVER['PWD'] . '/vendor/autoload.php');
}

foreach ($options as $file) {
	if (file_exists($file)) {
		define('SNIFFER_COMPOSER_INSTALL', $file);

		break;
	}
}
require SNIFFER_COMPOSER_INSTALL;

$sniffer = new Sniffer($argv);

$sniffer->sniff();
echo 0;
