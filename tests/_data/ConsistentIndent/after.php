<?php

namespace App\Test;

class ConsistentIndentTest {

	public function testMethod() {
		if (!isset($this->params['url']['filter'])) {
			$filter1 = 'true';
		} else {
			$filter1 = $this->params['url']['filter'];
		}

		$orphaned1 = $this->method1(); // WRONG - orphaned after }
		$orphaned2 = $this->method2(); // WRONG - consecutive orphaned
		if (!$result) {
			$correct = true;
		}

		$callback = function () {
			return true;
		};
		$orphaned3 = 'test'; // WRONG - orphaned after };

		// Valid continuation - should NOT be flagged
		$continuation = 'some string ' . $var
			. ' continuation';
	}

}
