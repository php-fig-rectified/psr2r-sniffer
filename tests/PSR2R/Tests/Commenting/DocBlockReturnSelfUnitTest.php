<?php

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

/**
 * Class DocBlockReturnSelfUnitTest
 *
 * @author  Ed Barnard
 * @license MIT
 * @package PSR2R\Tests\Commenting
 */
class DocBlockReturnSelfUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			20 => 2,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}
