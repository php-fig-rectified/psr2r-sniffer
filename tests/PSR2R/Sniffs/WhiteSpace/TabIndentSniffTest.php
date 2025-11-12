<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Sniffs\WhiteSpace\TabIndentSniff;
use PSR2R\Test\TestCase;

class TabIndentSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testTabIndentSniffer(): void {
		$this->assertSnifferFindsErrors(new TabIndentSniff(), 4);
	}

	/**
	 * @return void
	 */
	public function testTabIndentFixer(): void {
		$this->assertSnifferCanFixErrors(new TabIndentSniff());
	}

}
