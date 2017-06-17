<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 2:44 PM
 */

namespace PSR2R\Tests\Classes;

use PSR2R\Base\AbstractBase;

class ClassCreateInstanceUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [9 => 1];
	}

	protected function getWarningList() {
		return [];
	}
}
