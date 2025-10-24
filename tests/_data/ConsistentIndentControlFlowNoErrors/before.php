<?php

namespace App\Test;

class ConsistentIndentControlFlowNoErrorsTest {

	// Test 1: Valid return with correct indentation
	public function testValidReturn() {
		if ($condition) {
			$x = 1;
		}
		return $x; // CORRECT
	}

	// Test 2: Valid return with blank line and correct indentation
	public function testValidReturnWithBlank() {
		$y = 2;

		return $y; // CORRECT
	}

	// Test 3: Valid throw with correct indentation
	public function testValidThrow() {
		if (!$valid) {
			$error = 'Invalid';
		}
		throw new Exception($error); // CORRECT
	}

	// Test 4: Valid break in loop
	public function testValidBreakInLoop() {
		foreach ($items as $item) {
			if ($item === 'stop') {
				break; // CORRECT
			}
		}
	}

	// Test 5: Valid continue in loop
	public function testValidContinue() {
		foreach ($items as $item) {
			if ($item < 0) {
				continue; // CORRECT
			}
			$result[] = $item;
		}
	}

	// Test 6: Break in switch (allowed extra indent at case body level)
	public function testBreakInSwitch() {
		switch ($value) {
			case 1:
				$x = 1;
				break; // CORRECT
			case 2:
				$y = 2;

				break; // CORRECT - with blank line
		}
	}

	// Test 7: Nested returns
	public function testNestedReturns() {
		if ($condition1) {
			if ($condition2) {
				return true;
			}
			return false;
		}
		return null;
	}

}
