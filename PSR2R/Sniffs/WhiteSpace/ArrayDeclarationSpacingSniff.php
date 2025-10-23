<?php

namespace PSR2R\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PSR2R\Tools\AbstractSniff;

/**
 * Ensures that array opening brackets are on the same line as the assignment operator.
 * Prevents cases like:
 *
 *     public $components =
 *     [
 *
 * Should be:
 *
 *     public $components = [
 *
 * @author Mark Scherer
 * @license MIT
 */
class ArrayDeclarationSpacingSniff extends AbstractSniff {

	/**
	 * @inheritDoc
	 */
	public function register(): array {
		return [T_OPEN_SHORT_ARRAY];
	}

	/**
	 * @inheritDoc
	 */
	public function process(File $phpcsFile, int $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// Find the previous non-whitespace token
		$prevIndex = $phpcsFile->findPrevious(T_WHITESPACE, $stackPtr - 1, null, true);
		if ($prevIndex === false) {
			return;
		}

		// Check if the previous token is an assignment operator
		$assignmentTokens = [T_EQUAL, T_DOUBLE_ARROW];
		if (!in_array($tokens[$prevIndex]['code'], $assignmentTokens, true)) {
			return;
		}

		// Check if the opening bracket is on a different line than the assignment
		if ($tokens[$prevIndex]['line'] === $tokens[$stackPtr]['line']) {
			return;
		}

		// We found the issue: assignment on one line, bracket on another
		$fix = $phpcsFile->addFixableError(
			'Array opening bracket must be on the same line as the assignment operator',
			$stackPtr,
			'OpeningBracketNewline',
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		// Remove all whitespace between the assignment and the opening bracket
		for ($i = $prevIndex + 1; $i < $stackPtr; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		// Add a single space after the assignment operator
		$phpcsFile->fixer->addContent($prevIndex, ' ');

		$phpcsFile->fixer->endChangeset();
	}

}
