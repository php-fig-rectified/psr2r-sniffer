<?php

namespace PSR2R\Test\Sniffs\Commenting;

use PSR2R\Sniffs\Commenting\FullyQualifiedClassNameInDocBlockSniff;

/**
 */
class FullyQualifiedClassNameInDocBlockSniffTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @return void
	 */
	public function testInstance() {
		$this->assertTrue(class_exists('PSR2R\Sniffs\Commenting\FullyQualifiedClassNameInDocBlockSniff'));
		$sniff = new FullyQualifiedClassNameInDocBlockSniff();
	}

}
