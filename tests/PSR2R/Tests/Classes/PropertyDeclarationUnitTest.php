<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 2:56 PM
 */

namespace PSR2R\Tests\Classes;

use PSR2R\Base\AbstractBase;

class PropertyDeclarationUnitTest extends AbstractBase {
	protected function getErrorList() {
		return [14 => 1,
			15 => 2,
			17 => 1,
			18 => 1,
			19 => 1,
			];
	}

	protected function getWarningList() {
		return [];
	}
}
