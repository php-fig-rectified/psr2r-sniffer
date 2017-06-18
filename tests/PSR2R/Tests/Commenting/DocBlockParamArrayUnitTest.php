<?php

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

/**
 * Class DocBlockParamArrayUnitTest
 *
 * @author  Ed Barnard
 * @license MIT
 * @package PSR2R\Tests\Commenting
 */
class DocBlockParamArrayUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			7 => 1,
		];
	}

	protected function getWarningList() {
		return [];
	}
}
