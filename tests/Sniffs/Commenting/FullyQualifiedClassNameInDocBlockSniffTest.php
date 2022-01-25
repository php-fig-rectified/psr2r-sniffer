<?php

namespace PSR2R\Tests\Commenting;

use PHPUnit\Framework\TestCase;
use PSR2R\Sniffs\Commenting\FullyQualifiedClassNameInDocBlockSniff;

class FullyQualifiedClassNameInDocBlockSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testInstance() {
		$this->assertTrue(class_exists(FullyQualifiedClassNameInDocBlockSniff::class));
		$sniff = new FullyQualifiedClassNameInDocBlockSniff();
		static::assertInstanceOf(FullyQualifiedClassNameInDocBlockSniff::class, $sniff);
	}

}
