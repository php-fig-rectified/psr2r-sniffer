<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Sniffs\WhiteSpace\ConsistentIndentSniff;
use PSR2R\Test\TestCase;

/**
 * Tests that ConsistentIndentSniff does not flag valid control flow patterns.
 */
class ConsistentIndentControlFlowNoErrorsSniffTest extends TestCase {

	/**
	 * Test that valid control flow patterns are not flagged.
	 *
	 * @return void
	 */
	public function testConsistentIndentControlFlowNoErrors(): void {
		$this->assertSnifferFindsErrors(new ConsistentIndentSniff(), 0);
	}

	/**
	 * Test that no changes are made to valid code.
	 *
	 * @return void
	 */
	public function testConsistentIndentControlFlowNoErrorsFixer(): void {
		$this->assertSnifferCanFixErrors(new ConsistentIndentSniff(), 0);
	}

	/**
	 * @return string
	 */
	protected function testFilePath(): string {
		return implode(DIRECTORY_SEPARATOR, [
			__DIR__,
			'..', '..', '..', '_data',
		]) . DIRECTORY_SEPARATOR;
	}

	/**
	 * @param \PHP_CodeSniffer\Sniffs\Sniff $sniffer
	 *
	 * @return string
	 */
	protected function getDummyFileBefore($sniffer): string {
		$className = 'ConsistentIndentControlFlowNoErrors';

		$file = $this->testFilePath() . $className . DIRECTORY_SEPARATOR . static::FILE_BEFORE;
		if (!file_exists($file)) {
			$this->fail(sprintf('File not found: %s.', $file));
		}

		return $file;
	}

	/**
	 * @param \PHP_CodeSniffer\Sniffs\Sniff $sniffer
	 *
	 * @return string
	 */
	protected function getDummyFileAfter($sniffer): string {
		$className = 'ConsistentIndentControlFlowNoErrors';

		$file = $this->testFilePath() . $className . DIRECTORY_SEPARATOR . static::FILE_AFTER;
		if (!file_exists($file)) {
			$this->fail(sprintf('File not found: %s.', $file));
		}

		return $file;
	}

}
