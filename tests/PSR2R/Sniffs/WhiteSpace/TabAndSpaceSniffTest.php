<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Sniffs\WhiteSpace\TabAndSpaceSniff;
use PSR2R\Test\TestCase;

class TabAndSpaceSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testTabAndSpaceSniffer(): void {
		$this->assertSnifferFindsErrors(new TabAndSpaceSniff(), 4);
	}

	/**
	 * @return void
	 */
	public function testTabAndSpaceFixer(): void {
		$this->assertSnifferCanFixErrors(new TabAndSpaceSniff());
	}

}
