<?php

/** @noinspection UsingInclusionOnceReturnValueInspection */
$autoloader = require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
	'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// The code sniffer autoloader
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
	'vendor' . DIRECTORY_SEPARATOR .
	'squizlabs' . DIRECTORY_SEPARATOR .
	'php_codesniffer' . DIRECTORY_SEPARATOR .
	'autoload.php';

// The test wrapper
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR .
	'vendor' . DIRECTORY_SEPARATOR .
	'squizlabs' . DIRECTORY_SEPARATOR .
	'php_codesniffer' . DIRECTORY_SEPARATOR .
	'tests' . DIRECTORY_SEPARATOR .
	'AllTests.php';

if (is_object($autoloader)) {
	$GLOBALS['finder'] = $autoloader;
}
