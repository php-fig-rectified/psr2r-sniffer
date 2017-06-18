<?php

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

/**
 * Class DocBlockReturnTagUnitTest
 *
 * @author  Ed Barnard
 * @license MIT
 * @package PSR2R\Tests\Commenting
 */
class DocBlockReturnTagUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			15 => 1,
			22 => 1,
			31 => 1,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}