<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 11:54 AM
 */

namespace PSR2RT;

use PHP_CodeSniffer\Autoload;
use PHP_CodeSniffer\Tests\PHP_CodeSniffer_AllTests;
use PHP_CodeSniffer\Tests\TestSuite;
use PHP_CodeSniffer\Util\Standards;
use PSR2R\Tools\AbstractSniff;

class AllTests extends PHP_CodeSniffer_AllTests {
	/**
	 * Add all PHP_CodeSniffer test suites into a single test suite.
	 *
	 * @return \PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'] = array();
		$GLOBALS['PHP_CODESNIFFER_TEST_DIRS'] = array();

		// Use a special PHP_CodeSniffer test suite so that we can
		// unset our autoload function after the run.
		$suite = new TestSuite('PHP PSR2R CodeSniffer');
		$suite->addTest(static::psr2rSuite());

		return $suite;

	}//end suite()

	/**
	 * Based on PHP_CodeSniffer/tests/Standards/AllSniffs.php
	 *
	 * @return \PHPUnit_Framework_TestSuite
	 */
	public static function psr2rSuite() {
		$GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'] = array();
		$GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = array();

		$suite = new \PHPUnit_Framework_TestSuite('PHP PSR2R Standards');
		$toolFile = $GLOBALS['finder']->findFile(AbstractSniff::class);
		$standardDir = dirname(dirname(realpath($toolFile)));
		$testsDir = __DIR__ . DIRECTORY_SEPARATOR . 'PSR2R' . DIRECTORY_SEPARATOR . 'Tests' . DIRECTORY_SEPARATOR;
		$di = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($testsDir));
		foreach ($di as $file) {
			// Skip hidden files
			if ('.' === $file->getFilename()[0]) {
				continue;
			}

			// Tests must have the extension 'php'
			$parts = explode('.', $file);
			$ext = array_pop($parts);
			if ('php' !== $ext) {
				continue;
			}

			$className = Autoload::loadFile($file->getPathname());
			$GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'][$className] = $standardDir;
			$GLOBALS['PHP_CODESNIFFER_TEST_DIRS'][$className] = $testsDir;
			$suite->addTestSuite($className);
		}
		return $suite;
	}

}
