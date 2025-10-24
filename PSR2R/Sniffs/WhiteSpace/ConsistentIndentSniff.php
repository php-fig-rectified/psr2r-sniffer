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

		// Skip case/default - they have special indentation rules in switch statements
		if (in_array($tokens[$nextToken]['code'], [T_CASE, T_DEFAULT], true)) {
			return;
		}

		// Get the expected indentation based on scope
		$expectedIndent = $this->getExpectedIndent($phpcsFile, $nextToken, $tokens);

		// Special handling for break/continue in switch statements
		// They can be indented one level deeper (at case body level)
		if (in_array($tokens[$nextToken]['code'], [T_BREAK, T_CONTINUE], true)) {
			if ($this->isInSwitch($tokens, $nextToken)) {
				// Allow one extra level of indentation for break/continue in switch
				// (they're typically at the same level as case body code)
				if ($currentIndent === $expectedIndent + 1) {
					return;
				}
			}
		}

		// Check if line is over-indented (more than expected for its scope)
		if ($currentIndent > $expectedIndent) {
			// Check if this line starts with a continuation operator
			if ($this->startsWithContinuationOperator($nextToken, $tokens)) {
				return; // Valid continuation line
			}

			$prevLine = $this->findPreviousContentLine($phpcsFile, $stackPtr, $tokens);

			// Check if this is a valid continuation line or incorrectly indented
			if ($prevLine !== null && !$this->isValidContinuation($phpcsFile, $prevLine, $tokens)) {
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
	 * Check if there's a blank line before the current line.
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $stackPtr
	 * @param array $tokens
	 *
	 * @return bool
	 */
	protected function hasBlankLineBefore(File $phpcsFile, int $stackPtr, array $tokens): bool {
		$currentLine = $tokens[$stackPtr]['line'];

		// Find the previous line
		$prevLine = $currentLine - 1;
		if ($prevLine < 1) {
			return false;
		}

		// Check if the previous line is empty (only whitespace or completely blank)
		for ($i = $stackPtr - 1; $i >= 0; $i--) {
			if ($tokens[$i]['line'] < $prevLine) {
				// We've gone past the previous line
				break;
			}

			if ($tokens[$i]['line'] === $prevLine) {
				// Found a token on the previous line
				if ($tokens[$i]['code'] !== T_WHITESPACE) {
					// Previous line has content
					return false;
				}
			}
		}

		// Previous line was empty (only whitespace or no tokens)
		return true;
	}

	/**
	 * Check if the token is inside a switch statement.
	 *
	 * @param array $tokens
	 * @param int $stackPtr
	 *
	 * @return bool
	 */
	protected function isInSwitch(array $tokens, int $stackPtr): bool {
		$conditions = $tokens[$stackPtr]['conditions'];

		return in_array(T_SWITCH, $conditions, true);
	}

	/**
	 * Check if this line starts with a continuation operator.
	 *
	 * @param int $nextToken First non-whitespace token on the line
	 * @param array $tokens
	 *
	 * @return bool
	 */
	protected function startsWithContinuationOperator(int $nextToken, array $tokens): bool {
		$continuationStarters = [
			\T_STRING_CONCAT,
			\T_OBJECT_OPERATOR,
			\T_NULLSAFE_OBJECT_OPERATOR,
			\T_BOOLEAN_AND,
			\T_BOOLEAN_OR,
			\T_LOGICAL_AND,
			\T_LOGICAL_OR,
			\T_PLUS,
			\T_MINUS,
			\T_MULTIPLY,
			\T_DIVIDE,
		];

		return in_array($tokens[$nextToken]['code'], $continuationStarters, true);
	}

	/**
	 * Check if this looks like a valid continuation line (allowed to have extra indentation).
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $prevToken Previous content token
	 * @param array $tokens
	 *
	 * @return bool True if this is a valid continuation, false if it should match scope indent
	 */
	protected function isValidContinuation(File $phpcsFile, int $prevToken, array $tokens): bool {
		$prevCode = $tokens[$prevToken]['code'];

		// Tokens that indicate the next line is a continuation
		$continuationTokens = [
			\T_PLUS,
			\T_MINUS,
			\T_MULTIPLY,
			\T_DIVIDE,
			\T_MODULUS,
			\T_STRING_CONCAT,
			\T_COMMA,
			\T_OPEN_PARENTHESIS,
			\T_OPEN_SQUARE_BRACKET,
			\T_OPEN_SHORT_ARRAY,
			\T_DOUBLE_ARROW,
			\T_BOOLEAN_AND,
			\T_BOOLEAN_OR,
			\T_LOGICAL_AND,
			\T_LOGICAL_OR,
			\T_INSTANCEOF,
			\T_INLINE_THEN,
			\T_COALESCE,
			\T_OBJECT_OPERATOR,
			\T_NULLSAFE_OBJECT_OPERATOR,
			\T_EQUAL,
			\T_PLUS_EQUAL,
			\T_MINUS_EQUAL,
			\T_MUL_EQUAL,
			\T_DIV_EQUAL,
			\T_CONCAT_EQUAL,
			\T_MOD_EQUAL,
		];

		if (in_array($prevCode, $continuationTokens, true)) {
			return true;
		}

		// Check string representation for bracket tokens (PHPCS sometimes uses string codes)
		$prevContent = $tokens[$prevToken]['content'] ?? '';
		if ($prevContent === '[' || $prevContent === '(') {
			return true;
		}

		return false;
	}

}
