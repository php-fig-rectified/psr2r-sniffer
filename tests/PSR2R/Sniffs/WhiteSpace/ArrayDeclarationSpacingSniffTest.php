<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Sniffs\WhiteSpace\ArrayDeclarationSpacingSniff;
use PSR2R\Test\TestCase;

class ArrayDeclarationSpacingSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testArrayDeclarationSpacingSniffer(): void {
		$this->assertSnifferFindsErrors(new ArrayDeclarationSpacingSniff(), 4);
	}

	/**
	 * @return void
	 */
	public function testArrayDeclarationSpacingFixer(): void {
		$this->assertSnifferCanFixErrors(new ArrayDeclarationSpacingSniff());
	}

}
