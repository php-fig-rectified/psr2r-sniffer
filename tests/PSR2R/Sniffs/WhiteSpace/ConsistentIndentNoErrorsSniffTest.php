<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Test\PSR2R\Sniffs\WhiteSpace;

use PSR2R\Sniffs\WhiteSpace\ConsistentIndentSniff;
use PSR2R\Test\TestCase;

/**
 * Tests that valid code patterns are NOT flagged as errors (no false positives).
 */
class ConsistentIndentNoErrorsSniffTest extends TestCase {

	/**
	 * @return void
	 */
	public function testConsistentIndentNoErrors(): void {
		$this->assertSnifferFindsErrors(new ConsistentIndentSniff(), 0);
	}

	/**
	 * @return void
	 */
	public function testConsistentIndentNoErrorsFixer(): void {
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
		$className = 'ConsistentIndentNoErrors';

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
		$className = 'ConsistentIndentNoErrors';

		$file = $this->testFilePath() . $className . DIRECTORY_SEPARATOR . static::FILE_AFTER;
		if (!file_exists($file)) {
			$this->fail(sprintf('File not found: %s.', $file));
		}

		return $file;
	}

}
