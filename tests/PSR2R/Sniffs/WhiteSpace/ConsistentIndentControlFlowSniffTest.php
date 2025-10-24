<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Sniffs\WhiteSpace\ConsistentIndentSniff;
use PSR2R\Test\TestCase;

/**
 * Tests for ConsistentIndentSniff with control flow keywords (return, break, continue, throw).
 */
class ConsistentIndentControlFlowSniffTest extends TestCase {

	/**
	 * Test that the sniffer finds orphaned control flow statements.
	 *
	 * @return void
	 */
	public function testConsistentIndentControlFlowSniffer(): void {
		$errors = $this->runFixer(new ConsistentIndentSniff());
		$this->assertGreaterThan(0, count($errors), 'Should find at least one error');
	}

	/**
	 * Test that the fixer can fix orphaned control flow statements.
	 *
	 * @return void
	 */
	public function testConsistentIndentControlFlowFixer(): void {
		$this->assertSnifferCanFixErrors(new ConsistentIndentSniff());
	}

}
