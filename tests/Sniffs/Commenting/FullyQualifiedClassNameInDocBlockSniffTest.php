<?php

namespace PSR2R\Tests\Commenting;

use PHPUnit\Framework\TestCase;
use PSR2R\Sniffs\Commenting\DocBlockParamSniff;

/**
 */
class FullyQualifiedClassNameInDocBlockSniffTest extends TestCase {

	/**
	 * @return void
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 * @throws \PHPUnit_Framework_Exception
	 */
	public function testInstance() {
		$this->assertTrue(class_exists(DocBlockParamSniff::class));
		$sniff = new DocBlockParamSniff();
		static::assertInstanceOf(DocBlockParamSniff::class, $sniff);
	}

}
