<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Detects orphaned indentation - lines that are over-indented without a scope change.
 * This catches cases where code has extra indentation (e.g., leftover from a deleted block).
 *
 * @author Mark Scherer
 * @license MIT
 */
class ConsistentIndentSniff implements Sniff {

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
		$tokens = $phpcsFile->getTokens();

		// Only check whitespace at the start of lines (indentation)
		if ($stackPtr > 0 && $tokens[$stackPtr - 1]['line'] === $tokens[$stackPtr]['line']) {
			return;
		}

		$line = $tokens[$stackPtr]['line'];

		// Skip first line and lines in docblocks
		if ($line === 1 || !empty($tokens[$stackPtr]['nested_attributes'])) {
			return;
		}

		// Get the current indentation level
		$currentIndent = $this->getIndentLevel($tokens[$stackPtr]);
		if ($currentIndent === 0) {
			return;
		}

		// Find the next non-whitespace token on this line
		$nextToken = $phpcsFile->findNext(T_WHITESPACE, $stackPtr + 1, null, true);
		if ($nextToken === false || $tokens[$nextToken]['line'] !== $line) {
			// Empty line or no content
			return;
		}

		// Skip closing braces - they're allowed to be dedented
		if ($tokens[$nextToken]['code'] === T_CLOSE_CURLY_BRACKET) {
			return;
		}

		// Skip control flow keywords that often have blank lines before them
		$controlFlowTokens = [T_BREAK, T_CONTINUE, T_RETURN, T_THROW, T_CASE, T_DEFAULT];
		if (in_array($tokens[$nextToken]['code'], $controlFlowTokens, true)) {
			return;
		}

		// Get the expected indentation based on scope
		$expectedIndent = $this->getExpectedIndent($phpcsFile, $nextToken, $tokens);

		// Allow continuation lines (lines can be indented more for alignment)
		// But check if the previous line suggests this should NOT be indented more
		if ($currentIndent > $expectedIndent) {
			$prevLine = $this->findPreviousContentLine($phpcsFile, $stackPtr, $tokens);
			if ($prevLine !== null && $this->isOrphanedIndent($phpcsFile, $prevLine, $currentIndent, $expectedIndent, $tokens)) {
				$error = 'Line indented incorrectly; expected %d tabs, found %d';
				$data = [$expectedIndent, $currentIndent];
				$fix = $phpcsFile->addFixableError($error, $stackPtr, 'Incorrect', $data);

				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($stackPtr, str_repeat("\t", $expectedIndent));
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

	/**
	 * Get the indentation level (number of tabs) for a whitespace token.
	 *
	 * @param array $token
	 *
	 * @return int
	 */
	protected function getIndentLevel(array $token): int {
		$content = $token['orig_content'] ?? $token['content'];

		return substr_count($content, "\t");
	}

	/**
	 * Get the expected indentation level based on scope.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 * @param array $tokens
	 *
	 * @return int
	 */
	protected function getExpectedIndent(File $phpcsFile, int $stackPtr, array $tokens): int {
		$conditions = $tokens[$stackPtr]['conditions'];

		return count($conditions);
	}

	/**
	 * Find the previous line that has actual content (not blank, not comment-only).
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 * @param array $tokens
	 *
	 * @return int|null
	 */
	protected function findPreviousContentLine(File $phpcsFile, int $stackPtr, array $tokens): ?int {
		$currentLine = $tokens[$stackPtr]['line'];

		for ($i = $stackPtr - 1; $i >= 0; $i--) {
			if ($tokens[$i]['line'] >= $currentLine) {
				continue;
			}

			// Skip whitespace and comments
			if ($tokens[$i]['code'] === T_WHITESPACE || $tokens[$i]['code'] === T_COMMENT) {
				continue;
			}

			return $i;
		}

		return null;
	}

	/**
	 * Check if this looks like orphaned indentation (not a valid continuation).
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $prevToken Previous content token
	 * @param int $currentIndent
	 * @param int $expectedIndent
	 * @param array $tokens
	 *
	 * @return bool
	 */
	protected function isOrphanedIndent(File $phpcsFile, int $prevToken, int $currentIndent, int $expectedIndent, array $tokens): bool {
		// Pattern 1: Previous line was a closing brace
		// This catches: } \n    orphaned code
		if ($tokens[$prevToken]['code'] === T_CLOSE_CURLY_BRACKET) {
			return true;
		}

		// Pattern 2: Previous line ended with }; (closure, array, etc.)
		// This catches: }; \n    orphaned code
		if ($tokens[$prevToken]['code'] === T_SEMICOLON) {
			// Check if the token before semicolon is a closing brace
			$beforeSemicolon = $phpcsFile->findPrevious(T_WHITESPACE, $prevToken - 1, null, true);
			if ($beforeSemicolon !== false && $tokens[$beforeSemicolon]['code'] === T_CLOSE_CURLY_BRACKET) {
				return true;
			}

			// Pattern 3: Previous line is at the same over-indented level
			// This catches consecutive orphaned lines: } \n    line1; \n    line2;
			// Find the start of the previous line to check its indentation
			$prevLineStart = $prevToken;
			while ($prevLineStart > 0 && $tokens[$prevLineStart - 1]['line'] === $tokens[$prevToken]['line']) {
				$prevLineStart--;
			}

			// Check if previous line started with whitespace
			if ($tokens[$prevLineStart]['code'] === T_WHITESPACE) {
				$prevIndent = $this->getIndentLevel($tokens[$prevLineStart]);
				// If previous line was also over-indented at the same level, this is likely orphaned too
				if ($prevIndent === $currentIndent && $prevIndent > $expectedIndent) {
					return true;
				}
			}
		}

		return false;
	}

}
