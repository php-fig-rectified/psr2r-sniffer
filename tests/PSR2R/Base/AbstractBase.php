<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 11:54 AM
 */

namespace PSR2R\Base;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

abstract class AbstractBase extends AbstractSniffUnitTest {
	protected function setUp() {
		parent::setUp();
		$config = new Config();
		$config->cache = false;
		$ruleset = new Ruleset($config);
		$GLOBALS['PHP_CODESNIFFER_RULESET'] = $ruleset;
	}
}
