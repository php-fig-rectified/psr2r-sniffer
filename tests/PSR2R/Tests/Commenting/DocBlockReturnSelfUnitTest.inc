<?php

namespace PSR2R\Tests\Commenting;
/**
 * Class DocBlockReturnSelfUnitTest
 *
 * @package PSR2R\Tests\Commenting
 * @return $this
 */
class DocBlockReturnSelfUnitTestInc {
	/**
	 * @return $this
	 */
	public function one() {
		return $this;
	}

	/**
	 * @param $var
	 * @return self
	 */
	public function two($var) {
		return $this;
	}

	/**
	 * @return void
	 */
	public function three() {
		return $this;
	}

	/**
	 * @return self
	 */
	public static function four() {
		return self;
	}
}
