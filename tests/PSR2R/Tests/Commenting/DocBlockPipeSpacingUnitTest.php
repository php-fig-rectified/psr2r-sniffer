<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 12:51 PM
 */

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

class DocBlockPipeSpacingUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			19 => 1,
			25 => 1,
			31 => 1,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}
