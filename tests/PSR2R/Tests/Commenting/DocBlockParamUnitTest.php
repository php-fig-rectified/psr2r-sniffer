<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 12:51 PM
 */

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

class DocBlockParamUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			18 => 1,
		];
	}

	protected function getWarningList() {
		return [
		];
	}
}
