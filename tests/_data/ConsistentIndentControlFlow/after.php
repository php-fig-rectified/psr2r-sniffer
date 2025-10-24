<?php

namespace App\Test;

class ConsistentIndentControlFlowTest {

	// Test 1: Orphaned return without blank line (WRONG)
	public function testOrphanedReturn() {
		if ($condition) {
			$x = 1;
		}
		return $x; // WRONG - orphaned after }
	}

	// Test 2: Orphaned return with blank line (WRONG)
	public function testOrphanedReturnWithBlank() {
		if ($condition) {
			$y = 2;
		}

		return $y; // WRONG - orphaned even with blank line
	}

	// Test 3: Orphaned throw (WRONG)
	public function testOrphanedThrow() {
		if (!$valid) {
			$error = 'Invalid';
		}
		throw new Exception($error); // WRONG - orphaned
	}

	// Test 4: Orphaned break outside switch (WRONG)
	public function testOrphanedBreakInLoop() {
		foreach ($items as $item) {
			if ($item === 'stop') {
				break; // WRONG - over-indented break in loop
			}
		}
	}

	// Test 5: Orphaned continue (WRONG)
	public function testOrphanedContinue() {
		foreach ($items as $item) {
			if ($item < 0) {
				continue; // WRONG - over-indented
			}
			$result[] = $item;
		}
	}

	// Test 6: Multiple orphaned lines (WRONG)
	public function testMultipleOrphaned() {
		if ($condition) {
			$a = 1;
		}
		$b = 2; // WRONG
		$c = 3; // WRONG
		return [$a, $b, $c]; // WRONG
	}

}
