<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\ControlStructures;

use PSR2R\Sniffs\ControlStructures\UnneededElseSniff;
use PSR2R\Test\TestCase;

class UnneededElseSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testUnneededElseSniffer(): void {
		$this->assertSnifferFindsErrors(new UnneededElseSniff(), 6);
	}

	/**
	 * @return void
	 */
	public function testUnneededElseFixer(): void {
		$this->assertSnifferCanFixErrors(new UnneededElseSniff());
	}

}
