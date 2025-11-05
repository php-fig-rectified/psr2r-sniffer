<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link http://pear.php.net/package/PHP_CodeSniffer_CakePHP
 * @since CakePHP CodeSniffer 0.1.11
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Check for any line starting with 2 spaces - which would indicate space indenting
 * Also check for "\t " - a tab followed by a space, which is a common similar mistake
 */
class TabAndSpaceSniff implements Sniff {

	/**
	 * A list of tokenizers this sniff supports.
	 *
	 * @var array
	 */
	public array $supportedTokenizers = [
		'PHP',
		'JS',
		'CSS',
	];

	/**
	 * Maximum number of errors to fix in a single file to prevent timeout.
	 * This prevents "FAILED TO FIX" on very large files.
	 *
	 * @var int
	 */
	protected static int $fixCount = 0;

	/**
	 * @var int
	 */
	protected const MAX_FIXES_PER_FILE = 100;

	/**
	 * Track fixed positions to detect infinite loops.
	 * Maps "filename:stackPtr" to attempt count.
	 *
	 * @var array<string, int>
	 */
	protected static array $fixedPositions = [];

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_WHITESPACE];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPtr): void {
		// Reset counters at start of new file
		static $lastFile = null;
		if ($lastFile !== $phpcsFile->getFilename()) {
			static::$fixCount = 0;
			static::$fixedPositions = [];
			$lastFile = $phpcsFile->getFilename();
		}
		$tokens = $phpcsFile->getTokens();

		$line = $tokens[$stackPtr]['line'];
		// Only check whitespace at the start of lines (indentation)
		if ($stackPtr > 0 && $tokens[$stackPtr - 1]['line'] === $line) {
			return;
		}

		$content = $tokens[$stackPtr]['orig_content'] ?? $tokens[$stackPtr]['content'];

		// Detect infinite loops: if we've tried to fix this position more than twice, skip it
		$posKey = $phpcsFile->getFilename() . ':' . $stackPtr;
		if (isset(static::$fixedPositions[$posKey]) && static::$fixedPositions[$posKey] > 2) {
			// Infinite loop detected - report as non-fixable
			$error = 'Mixed tabs and spaces in indentation; use tabs only (cannot auto-fix due to conflict)';
			$phpcsFile->addError($error, $stackPtr, 'MixedIndentationConflict');

			return;
		}

		// Check for space followed by tab (wrong order)
		if (str_contains($content, ' ' . "\t")) {
			$error = 'Space followed by tab found in indentation; use tabs only';
			// Only mark as fixable if we haven't exceeded the limit
			if (static::$fixCount < static::MAX_FIXES_PER_FILE) {
				$fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceBeforeTab');
				if ($fix) {
					static::$fixCount++;
					// Track this fix attempt to detect infinite loops
					static::$fixedPositions[$posKey] = (static::$fixedPositions[$posKey] ?? 0) + 1;
					// Replace space+tab patterns with just tabs
					$fixed = str_replace(" \t", "\t", $content);
					if ($fixed !== $content) {
						$phpcsFile->fixer->replaceToken($stackPtr, $fixed);
					}
				}
			} else {
				// Report as non-fixable error to avoid timeout
				$phpcsFile->addError($error . ' (auto-fix limit reached)', $stackPtr, 'SpaceBeforeTabLimitReached');
			}
		}

		// Check for tab followed by space (mixed indentation)
		if (str_contains($content, "\t ")) {
			$error = 'Tab followed by space found in indentation; use tabs only';
			// Only mark as fixable if we haven't exceeded the limit
			if (static::$fixCount < static::MAX_FIXES_PER_FILE) {
				$fix = $phpcsFile->addFixableError($error, $stackPtr, 'TabAndSpace');
				if ($fix) {
					static::$fixCount++;
					// Track this fix attempt to detect infinite loops
					static::$fixedPositions[$posKey] = (static::$fixedPositions[$posKey] ?? 0) + 1;
					// Remove spaces after tabs at start of line
					$fixed = preg_replace('/^(\t+) +/', '$1', $content);
					// Only apply fix if content actually changed
					if ($fixed !== null && $fixed !== $content) {
						$phpcsFile->fixer->replaceToken($stackPtr, $fixed);
					}
				}
			} else {
				// Report as non-fixable error to avoid timeout
				$phpcsFile->addError($error . ' (auto-fix limit reached)', $stackPtr, 'TabAndSpaceLimitReached');
			}
		}
	}

}
