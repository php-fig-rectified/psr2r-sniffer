<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Test\TestCase;

/**
 * Test case for docblock indentation issue reported by user.
 *
 * This tests the specific scenario where:
 * - Class has opening brace on new line
 * - Blank line after opening brace
 * - Docblock at column 1 with space indentation
 *
 * KNOWN ISSUE: The docblock content lines (lines starting with ' *') are not
 * being converted to tabs. They should be '\t *' but remain as '     *'.
 * This is because DocBlockAlignmentSniff handles overall positioning but
 * doesn't ensure tab indentation for the content lines.
 *
 * The TabIndentSniff fix prevents the conflict between sniffs, allowing
 * the fix to complete, but the output still has spaces instead of tabs
 * in docblock content lines.
 */
class DocBlockIndentConflictTest extends TestCase {

	/**
	 * @return void
	 */
	public function testDocBlockIndentConflictFixer(): void {
		$pathBefore = $this->testFilePath() . 'DocBlockIndentConflict/before.php';
		$pathAfter = $this->testFilePath() . 'DocBlockIndentConflict/after.php';

		// Run with full PSR2R standard to test interaction between sniffs
		$this->runFullFixer($pathBefore, $pathAfter, null, null, true);
	}

}
