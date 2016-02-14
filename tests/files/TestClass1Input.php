<?php

namespace PSR2R\Test\Fixtures;

use \Bar\Foo;

	/**
	 * Class TestClass1Input
	 * @package PSR2R\Test\Fixtures
	 */
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
	public $y = 'y';

/**
 * @param \Bar\Foo
 *
 * @return void
 */
	public function replace() {
		// A comment
		$a = 'b'.$c;
		$a = 'b'  .  $c;
		$a = 'b'
			. $c;
		$a = 'b' .
			$c;

		$x = 5 + 5;
		$y = [1, 2];

		include('foo.bar');
		require_once ( $foo ) ;

		if ( $x + (int ) $y ) {
		}

		$x = ! $x;
		$x = $y - $x;
		$x = - $x;
		$x = ~ $x;
		$x = + $x;
		$x = ++ $x;
		$x = -- $x;
		$x = @ $x;

		$this -> foo();
		self :: foo();

		$this
			-> foo();
		self
			:: foo();
	}
}
