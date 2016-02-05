<?php

namespace PSR2R\Test\Fixtures;

use \Bar\Foo;

class TestClass1Input {

	/**
	 * @param \Bar\Foo
	 *
	 * @return void
	 */
	public $x = 'y';

/**
 * @param \Bar\Foo
 *
 * @return void
 */
	public function replace() {
		// A comment
		$x = 5 + 5;
		$y = array(1, 2);
	}
}
