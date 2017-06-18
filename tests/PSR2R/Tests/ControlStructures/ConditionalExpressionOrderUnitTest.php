<?php

namespace PSR2R\Tests\ControlStructures;

use PSR2R\Base\AbstractBase;

/**
 * Class ConditionalExpressionOrderUnitTest
 *
 * @author  Ed Barnard
 * @license MIT
 * @package PSR2R\Tests\ControlStructures
 */
class ConditionalExpressionOrderUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			16 => 1,
			19 => 1,
			22 => 1,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}
