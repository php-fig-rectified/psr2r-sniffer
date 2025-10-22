<?php declare(strict_types = 1);

namespace PSR2R;

class ControlStructureSpacingExample {

	/**
	 * Test case: else if on different line from closing brace
	 *
	 * @param int $rating
	 *
	 * @return string
	 */
	public function testElseIfSpacing(int $rating): string {
		if ($rating >= 2400) {
			return 'Expert';
		}
		else if ($rating >= 1800) {
			return 'Advanced';
		}
		else {
			return 'Beginner';
		}
	}

	/**
	 * Test case: else if with opening brace on different line
	 *
	 * @param int $score
	 *
	 * @return string
	 */
	public function testElseIfBraceSpacing(int $score): string {
		if ($score >= 90) {
			return 'A';
		}
		else if ($score >= 80)
		{
			return 'B';
		}
		else
		{
			return 'F';
		}
	}

	/**
	 * Test case: elseif on different line from closing brace
	 *
	 * @param int $value
	 *
	 * @return string
	 */
	public function testElseifSpacing(int $value): string {
		if ($value > 100) {
			return 'high';
		}
		elseif ($value > 50) {
			return 'medium';
		}
		else {
			return 'low';
		}
	}

	/**
	 * Test case: Complex nested structure
	 *
	 * @param int $x
	 * @param int $y
	 *
	 * @return string
	 */
	public function testNestedStructures(int $x, int $y): string {
		if ($x > 0) {
			if ($y > 0) {
				return 'both positive';
			}
			else {
				return 'x positive, y negative';
			}
		}
		else if ($x < 0) {
			if ($y > 0) {
				return 'x negative, y positive';
			}
			else {
				return 'both negative';
			}
		}
		else {
			return 'x is zero';
		}
	}

	/**
	 * Test case: Cookie handling example from user
	 *
	 * @return void
	 */
	public function testCookieHandling(): void {
		if (isset($_COOKIE['lastProfileLeft']) && $_COOKIE['lastProfileLeft'] != '0') {
			$lastProfileLeft = $_COOKIE['lastProfileLeft'];
			$this->lastProfileLeft = $lastProfileLeft;
		}
		else {
			$this->lastProfileLeft = null;
		}
	}

}
