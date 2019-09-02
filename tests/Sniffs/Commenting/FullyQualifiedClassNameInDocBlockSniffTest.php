<?php

namespace PSR2R\Tests\Commenting;

use PHPUnit\Framework\TestCase;
use PSR2R\Sniffs\Commenting\FullyQualifiedClassNameInDocBlockSniff;

class FullyQualifiedClassNameInDocBlockSniffTest extends TestCase {

	/**
	 * @return void
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 * @throws \PHPUnit_Framework_Exception
	 */
	public function testInstance() {
		$this->assertTrue(class_exists(FullyQualifiedClassNameInDocBlockSniff::class));
		$sniff = new FullyQualifiedClassNameInDocBlockSniff();
		static::assertInstanceOf(FullyQualifiedClassNameInDocBlockSniff::class, $sniff);
	}

}
