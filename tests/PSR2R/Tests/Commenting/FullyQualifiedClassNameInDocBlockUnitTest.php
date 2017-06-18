<?php

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

/**
 * Class FullyQualifiedClassNameInDocBlockUnitTest
 *
 * @author  Ed Barnard
 * @license MIT
 * @package PSR2R\Tests\Commenting
 */
class FullyQualifiedClassNameInDocBlockUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			26 => 3,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}
