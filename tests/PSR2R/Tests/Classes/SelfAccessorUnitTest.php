<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 2:56 PM
 */

namespace PSR2R\Tests\Classes;

use PSR2R\Base\AbstractBase;

class SelfAccessorUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [
			21 => 1,
			32 => 1,
			];
	}

	protected function getWarningList() {
		return [];
	}
}
