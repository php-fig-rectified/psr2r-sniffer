<?php

namespace App\Test;

class ConsistentIndentNoErrorsTest {

	// Test 1: Switch with breaks (should NOT flag)
	public function testSwitch($value) {
		switch ($value) {
			case 1:
				$x = 1;

				break;
			case 2:
				$y = 2;

				break;
			default:
				$z = 3;

				break;
		}
	}

	// Test 2: Array with blank lines (should NOT flag)
	public function testArray() {
		$config = [
			'db' => ['host' => 'localhost'],

			'cache' => ['driver' => 'redis'],

			'queue' => ['connection' => 'sync'],
		];

		return $config;
	}

	// Test 3: Chained methods (should NOT flag)
	public function testChain() {
		$result = $this->obj
			->method1()
			->method2()
			->method3();

		return $result;
	}

	// Test 4: Multi-line concatenation (should NOT flag)
	public function testConcat() {
		$error = 'Use statement ' . $extracted
			. ' for ' . $className
			. ' should be in use block.';

		return $error;
	}

	// Test 5: Multi-line function call (should NOT flag)
	public function testFunctionCall() {
		$result = someFunction(
			$param1,
			$param2,
			$param3
		);

		return $result;
	}

	// Test 6: Multiple methods (should NOT flag)
	public function method1() {
		return 1;
	}

	public function method2() {
		return 2;
	}

	public function method3() {
		return 3;
	}

	// Test 7: Try-catch-finally (should NOT flag)
	public function testTryCatch() {
		try {
			$x = 1;
		} catch (Exception $e) {
			$y = 2;
		} finally {
			$z = 3;
		}

		return true;
	}

	// Test 8: Nested blocks (should NOT flag)
	public function testNested() {
		if ($condition1) {
			if ($condition2) {
				$x = 1;
			}
		}

		foreach ($items as $item) {
			if ($item > 0) {
				$result[] = $item;
			}
		}

		return true;
	}

	// Test 9: Return statements with blank lines (should NOT flag)
	public function testReturn() {
		$x = 1;

		return $x;
	}

	// Test 10: Throw statements (should NOT flag)
	public function testThrow() {
		$error = new Exception('Error');

		throw $error;
	}

	// Test 11: Continue in loop (should NOT flag)
	public function testContinue($items) {
		foreach ($items as $item) {
			if ($item < 0) {
				continue;
			}

			$result[] = $item;
		}

		return true;
	}

	// Test 12: Array with docblock between elements (should NOT flag)
	public function testArrayWithDocblock() {
		$config = [
			'Datasources' => [
				'default' => [
					'password' => env('DB_PASSWORD', ''),
					'database' => env('DB_DATABASE', 'sandbox_local'), // Set in your app_local.php
				],

				/**
				 * The test connection is used during the test suite.
				 */
		'test' => [
					'password' => env('DB_PASSWORD', ''),
					'database' => 'cakephp_test',
				],
			],
		];

		return $config;
	}

}
