<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 12:51 PM
 */

namespace PSR2R\Tests\Classes;

use PSR2R\Base\AbstractBase;

class BraceOnSameLineUnitTest extends AbstractBase {
	protected function getErrorList($file = '') {
		switch ($file) {
			case 'BraceOnSameLineUnitTest.1.inc':
				return [];
			case 'BraceOnSameLineUnitTest.2.inc':
				return [
					12 => 1,
					20 => 1,
					28 => 1,
					37 => 1,
					40 => 1,
					];
		}
		return [];
	}

	protected function getWarningList($file = '') {
		return [];
	}
}
