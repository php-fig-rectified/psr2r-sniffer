<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\ControlStructures;

use PSR2R\Sniffs\ControlStructures\ControlStructureSpacingSniff;
use PSR2R\Test\TestCase;

class ControlStructureSpacingSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testControlStructureSpacingSniffer(): void {
		$this->assertSnifferFindsErrors(new ControlStructureSpacingSniff(), 13);
	}

	/**
	 * @return void
	 */
	public function testControlStructureSpacingFixer(): void {
		$this->assertSnifferCanFixErrors(new ControlStructureSpacingSniff());
	}

}
