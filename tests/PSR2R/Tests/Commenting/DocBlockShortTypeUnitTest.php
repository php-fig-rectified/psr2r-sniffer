<?php

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

/**
 * Class DocBlockShortTypeUnitTest
 *
 * @author  Ed Barnard
 * @license MIT
 * @package PSR2R\Tests\Commenting
 */
class DocBlockShortTypeUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			9 => 3,
			10 => 3,
			20 => 4,
			27 => 4,
			29 => 4,
			30 => 4,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}
