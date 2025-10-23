<?php declare(strict_types = 1);

namespace PSR2R;

class ArrayDeclarationSpacingExample {

	/**
	 * Test case: class property array with opening bracket on new line
	 *
	 * @var array
	 */
	public $components = [
		'RequestHandler',
		'Flash',
		'Auth',
	];

	/**
	 * @var array
	 */
	protected array $helpers = [
		'Html',
		'Form',
	];

	/**
	 * Test case: variable assignment with array on new line
	 *
	 * @return void
	 */
	public function testVariableAssignment(): void {
		$config = [
			'debug' => true,
			'cache' => false,
		];

		$options = [
			'verbose' => true,
			'timeout' => 30,
		];
	}

	/**
	 * Test case: Correct formatting (should not be flagged)
	 *
	 * @return void
	 */
	public function testCorrectFormatting(): void {
		$correct = [
			'a' => 'b',
			'c' => 'd',
		];

		$alsoCorrect = ['x', 'y', 'z'];
	}

}
