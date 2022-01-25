<?php

namespace PSR2R\Test\Sniffs\Commenting;

use PHP_CodeSniffer\Tests\Standards\AbstractSniffUnitTest;

class FullyQualifiedClassNameInDocBlockUnitTest extends AbstractSniffUnitTest {

	/**
	 * @inheritDoc
	 */
	public function getErrorList() {
		return [
			//9 => 1,
		];
	}
	/**
	 * @inheritDoc
	 */
	public function getWarningList() {
		return [
		];
	}

}
