<?php

namespace App\Test;

class TabAndSpaceTest {

	public function testMethod() {
		$correct = 1; // 2 tabs (correct)
		$tabSpace = 2; // 2 tabs + 1 space (WRONG)
		$spaceTabs = 3; // 1 space + 2 tabs (WRONG)
		$tabSpaceSpace = 4; // 2 tabs + 2 spaces (WRONG)

		if ($correct) {
			$nested = true;
			$nestedTabSpace = true; // WRONG
		}
	}

}
