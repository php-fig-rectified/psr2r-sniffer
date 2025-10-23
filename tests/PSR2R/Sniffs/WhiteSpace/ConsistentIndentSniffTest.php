<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Sniffs\WhiteSpace\ConsistentIndentSniff;
use PSR2R\Test\TestCase;

class ConsistentIndentSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testConsistentIndentSniffer(): void {
		$this->assertSnifferFindsErrors(new ConsistentIndentSniff(), 3);
	}

	/**
	 * @return void
	 */
	public function testConsistentIndentFixer(): void {
		$this->assertSnifferCanFixErrors(new ConsistentIndentSniff());
	}

}
