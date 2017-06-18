<?php
/**
 * Created by PhpStorm.
 * User: ewb
 * Date: 6/17/17
 * Time: 12:51 PM
 */

namespace PSR2R\Tests\Commenting;

use PSR2R\Base\AbstractBase;

class DocBlockParamUnitTestInc {
	/**
	 * @param $arr
	 * @return int
	 */
	public function one(array $arr = null, $scalar = 1) {
		return true;
	}
}
