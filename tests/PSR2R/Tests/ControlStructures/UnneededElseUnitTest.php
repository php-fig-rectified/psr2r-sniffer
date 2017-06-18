<?php

namespace PSR2R\Tests\ControlStructures;

use PSR2R\Base\AbstractBase;

/**
 * Class UnneededElseUnitTest
 *
 * @author  Ed Barnard
 * @license MIT
 * @package PSR2R\Tests\ControlStructures
 */
class UnneededElseUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			18 => 1,
			33 => 1,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}
